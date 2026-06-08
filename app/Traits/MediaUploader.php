<?php

namespace App\Traits;

use App\Services\Helper\ImageOptimizer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Polyfill\Uuid\Uuid;

/**
 * Trait MediaUploader
 *
 * Provides utility methods for handling file uploads, deletions,
 * and retrieving full file paths using Laravel's storage system.
 *
 * @author Md Safiullah
 *
 * @version 1.0
 *
 * @methods
 *  upload(mixed $file, string $path = 'others'): string
 *      Uploads the given file to the specified storage path and returns the file's storage path.
 *  delete(string $path): bool
 *      Deletes the file at the specified path from storage.
 *  getFullPath(string $path): string
 *      Retrieves the full public URL for the file located at the given storage path.
 *
 * @created 2024-09-04
 */
trait MediaUploader
{
    private static $base_path = '/frontend-uploads/';

    /**
     * Store the file in the storage
     *
     * @param  mixed  $file  The file to be uploaded
     * @param  string  $path  The path where the file will be stored. Default is 'others'
     * @return string The full path of the uploaded file
     */
    public function upload(mixed $file, string $path = 'others'): string
    {
        // optimize image if file is image
        if (in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
            $file = ImageOptimizer::optimize($file);
        }

        // Generate a random file name
        $file_name = Uuid::uuid_create(4).'-'.$file->getClientOriginalName();

        // Get the storage disk
        $storage = Storage::disk();

        // Set the full path where the file will be stored
        // Example: uploads/service/08-02-2022/5451545-example.png
        $full_path = self::$base_path.$path.'/'.Carbon::now()->format('d-m-Y').'/';

        // Check if the directory where the file will be stored exists
        // If it doesn't exist, create it
        if (! $storage->exists($full_path)) {
            $storage->makeDirectory($full_path);
        }

        // Store the file in the specified path
        if (in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
            $storage->putFileAs(
                $full_path,
                $file,
                $file_name,
                ['ContentType' => 'image/webp']
            );
        } else {
            $storage->putFileAs(
                $full_path,
                $file,
                $file_name
            );
        }

        $file_path = '/storage'.$full_path.$file_name;

        // Return the full path of the uploaded file
        return $file_path;
    }

    /**
     * Delete the file from the storage
     */
    public function delete($path)
    {
        $storage = Storage::disk();
        $path = str_ireplace('/storage', '', $path);
        if ($storage->exists($path)) {
            return $storage->delete($path);
        }

        return false;
    }

    /**
     * Get the full path
     */
    public function getFullPath($path)
    {
        return Storage::disk()->url($path);
    }
}
