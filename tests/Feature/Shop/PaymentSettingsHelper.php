<?php

namespace Tests\Feature\Shop;

use App\Models\Option;

/**
 * Test helpers for option-backed settings.
 *
 * Option caches all rows in a static `$autoload` array the first time any option
 * is read in a process. Tests therefore reset that cache after writing options so
 * the new values are seen by the code under test.
 */
trait PaymentSettingsHelper
{
    protected function setOptions(array $options): void
    {
        foreach ($options as $key => $value) {
            Option::updateOption($key, $value);
        }

        $this->resetOptionCache();
    }

    protected function resetOptionCache(): void
    {
        $ref = new \ReflectionProperty(Option::class, 'autoload');
        $ref->setAccessible(true);
        $ref->setValue(null, null);
    }

    protected function enableSslcommerz(): void
    {
        $this->setOptions([
            'sslcommerz_enabled' => 1,
            'sslcommerz_test_mode' => 1,
            'sslcommerz_store_id' => 'testbox',
            'sslcommerz_store_password' => 'qwerty',
        ]);
    }
}
