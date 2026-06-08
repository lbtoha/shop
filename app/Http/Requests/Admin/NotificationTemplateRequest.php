<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NotificationTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'is_active' => ['required', 'in:0,1'],
        ];

        if ($this->method() !== 'POST') {
            $rules['channel'] = ['nullable', 'in:email,sms,push'];
            $rules['name'] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }
}
