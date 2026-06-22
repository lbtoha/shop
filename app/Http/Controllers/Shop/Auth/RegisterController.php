<?php

namespace App\Http\Controllers\Shop\Auth;

use App\Enums\UserStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create()
    {
        if (auth()->check()) {
            return redirect()->route('shop.account.index');
        }

        return view('shop.auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            // Either email or phone is required; both may be given.
            'email' => 'nullable|required_without:phone|email|max:255|unique:users,email',
            'phone' => 'nullable|required_without:email|string|max:30|unique:users,phone',
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'email.required_without' => __('Please provide an email or a phone number.'),
            'phone.required_without' => __('Please provide a phone number or an email.'),
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'username' => $this->uniqueUsername($validated['email'] ?? $validated['phone']),
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => UserStatusEnum::ACTIVE->value,
        ]);

        auth()->login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('shop.account.index'))->with('success', __('Welcome! Your account has been created.'));
    }

    private function uniqueUsername(string $identifier): string
    {
        // Works for both email (slug of the local part) and phone numbers.
        $base = Str::slug(Str::before($identifier, '@'), '_') ?: 'user';
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base.'_'.$i++;
        }

        return $username;
    }
}
