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
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30|unique:users,phone',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'username' => $this->uniqueUsername($validated['email']),
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => UserStatusEnum::ACTIVE->value,
        ]);

        auth()->login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('shop.account.index'))->with('success', __('Welcome! Your account has been created.'));
    }

    private function uniqueUsername(string $email): string
    {
        $base = Str::slug(Str::before($email, '@'), '_') ?: 'user';
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base.'_'.$i++;
        }

        return $username;
    }
}
