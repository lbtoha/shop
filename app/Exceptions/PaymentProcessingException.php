<?php

namespace App\Exceptions;

use Exception;

class PaymentProcessingException extends Exception
{
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
        string $message = 'Payment processing failed',
        string $errorCode = 'PAYMENT_ERROR',
        int $code = 400,
        ?Exception $previous = null
    ) {
        $this->errorCode = $errorCode;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the custom error code.
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function render($request): mixed
    {
        if ($request->wantsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'code' => $this->getErrorCode(),
                'status' => $this->getCode(),
            ], $this->getCode());
        }

        if ($request->ajax()) {
            return response()->json([
                'message' => $this->getMessage(),
                'code' => $this->getErrorCode(),
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
