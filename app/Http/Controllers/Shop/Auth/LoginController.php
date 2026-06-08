<?php

namespace App\Http\Controllers\Shop\Auth;

use App\Enums\UserStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        if (auth()->check()) {
            return redirect()->route('shop.account.index');
        }

        return view('shop.auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = (bool) $request->boolean('remember');

        if (! auth()->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        if (auth()->user()->status !== UserStatusEnum::ACTIVE->value && auth()->user()->status !== UserStatusEnum::ACTIVE) {
            auth()->logout();
            throw ValidationException::withMessages([
                'email' => __('Your account is not active. Please contact support.'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('shop.account.index'));
    }

    public function destroy(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
