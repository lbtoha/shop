<?php

namespace App\Services\Helper;

use InvalidArgumentException;

class UniqueCodeGenerator
{
    /**
     * Generate a sequence code with letter prefix and padded numbers
     *
     * @param  string|object  $model  The model class or name
     * @param  string  $column  The column name to check for the latest value
     * @param  int  $minLength  Minimum length of the numeric part
     * @param  string|null  $defaultPrefix  Default prefix to use if no sequence exists
     * @return string Generated sequence code
     */
    public static function make($model, string $column, int $minLength = 6, ?string $defaultPrefix = 'TRACK', string $orderBy='created_at'): string
    {
        // Validate inputs
        if (empty($model) || empty($column)) {
            throw new InvalidArgumentException('Model and column are required');
        }

        // Get the latest record
        $latest = $model::query()->where($column, 'like', $defaultPrefix.'%')->latest($orderBy)->first();

        // If no records exist, return first sequence
        if (! $latest) {
            return $defaultPrefix.str_pad('1', $minLength, '0', STR_PAD_LEFT);
        }

        // Get the current sequence value
        $current = $latest->{$column};

        // Handle empty current value
        if (empty($current)) {
            return $defaultPrefix.str_pad('1', $minLength, '0', STR_PAD_LEFT);
        }

        // Generate new sequence with proper padding
        return self::incrementCode($current, $minLength, $defaultPrefix);
    }

    public static function incrementCode(string $current, int $minLength = 6, ?string $defaultPrefix = 'TRACK'): string
    {
        // Extract number part
        $prefixLength = strlen($defaultPrefix);
        $number = (int) substr($current, $prefixLength);

        // Calculate next number
        $next = $number + 1;

        // Calculate padding length - subtract prefix length from total desired length
        $paddingLength = $minLength;

        // Generate new sequence with proper padding
        return $defaultPrefix.str_pad((string) $next, $paddingLength, '0', STR_PAD_LEFT);
    }

    public static function generateRandomUniqueCode(): string
    {
        $code = uniqid();

        $time = time();

        return $code.$time;
    }
}
