<?php

namespace App\Services\Helper;

use App\Exceptions\CustomWebException;
use Exception;
use Illuminate\Support\Facades\Storage;
use JsonException;
use RuntimeException;
use SplFileInfo;

class JsonHelper
{
    private static $storage = null;

    public function __construct()
    {
        if (is_null(self::$storage)) {
            self::$storage = Storage::disk('lang');
        }
    }

    /**
     * Copies a language file from the helper directory to a language-specific directory.
     *
     * @param  string  $language_code  The language code for the destination directory.
     * @return string The path where the file was copied.
     */
    public function copyLanguageFileFromHelper(string $language_code): string
    {
        // Sanitize input to prevent directory traversal
        $language_code = basename($language_code);

        $copy_file_from_path = '/lang/help.json';
        $copy_file_to_path = "lang/{$language_code}.json";

        // Check if source file exists
        if (! self::$storage->exists($copy_file_from_path)) {
            throw new RuntimeException("Source file not found: {$copy_file_from_path}");
        }

        // Create directory if it doesn't exist
        $directory = dirname($copy_file_to_path);
        if (! self::$storage->exists($directory)) {
            self::$storage->makeDirectory($directory, 0755, true);
        }

        // Copy file if it doesn't exist
        if (! self::$storage->exists($copy_file_to_path)) {
            try {
                self::$storage->copy($copy_file_from_path, $copy_file_to_path);
            } catch (Exception $e) {
                throw new RuntimeException("Failed to copy file: {$e->getMessage()}");
            }
        }

        return $copy_file_to_path;
    }

    /**
     * Upload and process a language file
     *
     * @param  array  $validated  Validated data containing the language code
     * @return string The path where the file was stored
     *
     * @throws \InvalidArgumentException If invalid type provided
     * @throws \RuntimeException If file processing fails
     */
    public function uploadLanguageFile(array $validated): string
    {

        // Get the file from request
        $file = request()->file('language_file');
        if (! $file || ! $file->isValid()) {
            throw new \RuntimeException('Invalid or missing file upload');
        }

        // Validate file extension
        $allowedExtensions = ['json'];
        if (! in_array($file->extension(), $allowedExtensions)) {
            throw new \RuntimeException('Invalid file format. Only JSON files are allowed.');
        }

        // Read and validate JSON content
        try {
            $content = file_get_contents($file->getRealPath());
            $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            if (! is_array($json)) {
                throw new \RuntimeException('Invalid JSON structure');
            }

            // Remove duplicate keys and normalize the array
            $normalizedData = array_map(function ($value) {
                return is_string($value) ? trim($value) : $value;
            }, $json);

            // Encode back to JSON with proper formatting
            $processedContent = json_encode($normalizedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            // Create a temporary file with processed content
            $tempFile = tmpfile();
            fwrite($tempFile, $processedContent);
            $tempFilePath = stream_get_meta_data($tempFile)['uri'];

            // Upload the processed file
            $uploadPath = 'lang';
            $fileName = $validated['code'].'.json';

            $path = self::$storage->putFileAs(
                $uploadPath,
                $tempFilePath,
                $fileName,
                ['visibility' => 'private']
            );

            // Clean up
            fclose($tempFile);

            if (! $path) {
                throw new \RuntimeException('Failed to store the language file');
            }

            return $path;

        } catch (\JsonException $e) {
            throw new \RuntimeException('Invalid JSON content: '.$e->getMessage());
        } catch (\Exception $e) {
            throw new \RuntimeException('File processing failed: '.$e->getMessage());
        }
    }

    /**
     * Validates a JSON file for proper format and structure
     *
     * @param  string|SplFileInfo  $jsonFile  Path to JSON file or file object
     * @return bool True if JSON is valid
     *
     * @throws CustomWebException If JSON is invalid or file cannot be read
     */
    public function checkValidJson(string|SplFileInfo $jsonFile): bool
    {
        // Ensure the file exists and is readable
        $filePath = $jsonFile instanceof SplFileInfo ? $jsonFile->getRealPath() : $jsonFile;

        if (! file_exists(filename: $filePath)) {
            throw new CustomWebException(__('JSON file not found'));
        }

        if (! is_readable($filePath)) {
            throw new CustomWebException(__('JSON file is not readable'));
        }

        try {
            // Read file with memory limit check
            $fileSize = filesize($filePath);
            if ($fileSize > 10 * 1024 * 1024) { // 10MB limit
                throw new CustomWebException(__('JSON file is too large (max 10MB)'));
            }

            $content = file_get_contents($filePath);
            if ($content === false) {
                throw new CustomWebException(__('Could not read JSON file'));
            }

            // Decode JSON with detailed error checking
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            // Validate basic structure
            if (! is_array($data)) {
                throw new CustomWebException(__('JSON must contain an array or object'));
            }

            if (empty($data)) {
                throw new CustomWebException(__('JSON file cannot be empty'));
            }

            return true;

        } catch (JsonException $e) {
            // Provide more specific JSON error messages
            $message = match (json_last_error()) {
                JSON_ERROR_DEPTH => __('JSON nesting too deep'),
                JSON_ERROR_STATE_MISMATCH => __('JSON invalid or malformed'),
                JSON_ERROR_CTRL_CHAR => __('JSON contains unexpected control character'),
                JSON_ERROR_SYNTAX => __('JSON syntax error'),
                JSON_ERROR_UTF8 => __('JSON contains invalid UTF-8 characters'),
                default => __('JSON validation failed: ').$e->getMessage()
            };

            throw new CustomWebException($message);
        } catch (\Exception $e) {
            throw new CustomWebException(__('Error processing JSON file: ').$e->getMessage());
        }
    }

    public function deleteJsonFile(?string $file_path): bool
    {
        if ($file_path && self::$storage->exists($file_path)) {
            return self::$storage->delete($file_path);
        }

        return true;
    }
}
