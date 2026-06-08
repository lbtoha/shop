<?php

namespace App\Services\Helper;

use App\Facades\Flash;
use JsonException;

class JsonCleaner
{
    /**
     * Clean and validate JSON string
     *
     * @return array Returns cleaned and validated JSON as array
     *
     * @throws JsonException If JSON is invalid and cannot be fixed
     */
    public function cleanAndValidateJson(string $jsonString): array
    {
        // First try to decode as-is
        try {
            return json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            // If failed, try to clean and fix the JSON
            $cleanedJson = $this->cleanJson($jsonString);

            if (empty($cleanedJson)) {
                Flash::error('Invalid JSON string');

                return [];
            }

            return json_decode($cleanedJson, true, 512, JSON_THROW_ON_ERROR);
        }
    }

    /**
     * Clean invalid JSON string
     */
    private function cleanJson(string $jsonString): string
    {
        // Remove BOM and whitespace
        $jsonString = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', trim($jsonString));

        // Remove multiple commas
        $jsonString = preg_replace('/,\s*,/', ',', $jsonString);

        // Remove trailing commas in objects
        $jsonString = preg_replace('/,\s*}/', '}', $jsonString);

        // Remove trailing commas in arrays
        $jsonString = preg_replace('/,\s*\]/', ']', $jsonString);

        // Fix missing values (convert "key": , to "key": "")
        $jsonString = preg_replace('/"([^"]+)"\s*:\s*,/', '"$1": "",', $jsonString);

        // Remove any remaining invalid commas
        $jsonString = preg_replace('/,\s*$/', '', $jsonString);

        return $jsonString;
    }
}
