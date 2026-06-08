<?php

namespace App\Exceptions;

use App\Traits\Formatter;
use Exception;

class ApiException extends Exception
{
    use Formatter;

    public function render(string $message, array $context = []): string
    {
        return $this->withError($this->getMessage());
    }
}
