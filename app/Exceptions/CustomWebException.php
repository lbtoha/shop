<?php

namespace App\Exceptions;

use App\Traits\Formatter;
use Exception;

class CustomWebException extends Exception
{
    use Formatter;

    /**
     * The custom error code for the exception.
     *
     * @var string
     */
    protected $errorCode;

    /**
     * Constructor for the payment processing exception.
     */
    public function __construct(
        string $message = 'An error occurred',
        int $code = 400,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function render($request): mixed
    {
        if ($request->wantsJson()) {
            return $this->withError($this->getMessage());
        }

        if ($request->ajax()) {
            return response()->json([
                'message' => $this->getMessage(),
                'status' => $this->getCode(),
            ], $this->getCode());
        }

        return redirect()->back()->withError($this->getMessage());
    }

    /**
     * Provide additional context for logging.
     */
    public function context(): array
    {
        return [
            'error_code' => $this->errorCode,
        ];
    }
}
