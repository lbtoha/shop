<?php

namespace App\Exceptions;

use Exception;

class DemoException extends Exception
{
    /**
     * Constructor for the payment processing exception.
     */
    public function __construct(
        string $message = 'Add, edit, or delete data is not allowed in demo mode.',
    ) {
        parent::__construct($message, 400);
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
                'code' => 400,
                'status' => $this->getCode(),
            ], $this->getCode());
        }

        if ($request->ajax()) {
            return response()->json([
                'message' => $this->getMessage(),
                'code' => 400,
                'status' => $this->getCode(),
            ], $this->getCode());
        }

        return redirect()->back()->withError($this->getMessage());
    }
}
