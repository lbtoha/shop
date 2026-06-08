<?php

namespace App\Http\Requests\Admin;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'email_verified_at' => 'nullable|in:0,1',
            'phone_verified_at' => 'nullable|in:0,1',
            'is_kyc_verified' => 'nullable|in:0,1',
            'is_2fa_enabled' => 'required|in:0,1',
            'address' => 'nullable|string|max:250',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        if (isset($data['email_verified_at']) && $data['email_verified_at'] == 1) {
            $data['email_verified_at'] = Carbon::now();
        } else {
            $data['email_verified_at'] = null;
        }

        if (isset($data['phone_verified_at']) && $data['phone_verified_at'] == 1) {
            $data['phone_verified_at'] = Carbon::now();
        } else {
            $data['phone_verified_at'] = null;
        }

        if (isset($data['is_kyc_verified']) && $data['is_kyc_verified'] == 1) {
            $data['is_kyc_verified'] = true;
        } else {
            $data['is_kyc_verified'] = false;
        }

        if (isset($data['is_2fa_enabled']) && $data['is_2fa_enabled'] == 1) {
            $data['is_2fa_enabled'] = true;
        } else {
            $data['is_2fa_enabled'] = false;
        }

        return $data;
    }
}
