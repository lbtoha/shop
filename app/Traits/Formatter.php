<?php

namespace App\Traits;

use Illuminate\Validation\ValidationException;

trait Formatter
{
    private $http_status = [
        'unauthenticated_error' => 401,
        'with_error' => 417,
        'not_found' => 404,
        'validation_error' => 422,
        'success' => 200,
        'created' => 201,
    ];

    /**
     * formatting the message and data
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory | \Illuminate\Http\Response
     */
    public function withSuccess(mixed $data, string $message = '')
    {
        if (is_string($data)) {
            return $this->response($data, 'success');
        }

        return $this->response($message, 'success', $data);
    }

    public function withNotFound($data)
    {
        return $this->response($data, 'not_found');
    }

    public function withCreated($data)
    {
        if (is_string($data)) {
            return $this->response($data, 'created');
        }

        return $this->response('', 'created', $data);
    }

    public function withError($data)
    {
        return $this->response($data, 'with_error');
    }

    public function unauthenticatedError($data)
    {
        return $this->response($data, 'unauthenticated_error');
    }

    public function validationErrorResponse(ValidationException $exception)
    {
        $messages = array_map(function ($v) {
            return $v[0];
        }, $exception->errors());

        return $this->response($messages, 'validation_error');
    }

    /**
     * formatting the message and the exception
     *
     * @param  string|array  $messages
     * @param  string  $status
     * @param  mixed  $data
     * @return \Illuminate\Contracts\Routing\ResponseFactory | \Illuminate\Http\Response
     **/
    private function response($messages = '', $status = 'success', $data = [])
    {

        $response = [
            'statusCode' => $this->http_status[$status],
        ];

        if ($messages) {
            $response['message'] = $messages;
        }

        if (is_object($data) || is_array($data) && count($data)) {
            $response['data'] = $data;
        }

        if (count($response) === 1) {
            $response['data'] = [];
        }

        return response($response, $this->http_status[$status]);
    }
}
