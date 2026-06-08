<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LanguageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:languages,code,'.($this->method() == 'PUT' ? $this->language->id : null),
            'flag_code' => 'nullable|string|max:255',
            'language_file' => 'nullable|file|mimes:json',
            'is_default' => 'nullable|in:1,0',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        if (isset($data['language_file'])) {
            unset($data['language_file']);
        }

        return $data;
    }
}
