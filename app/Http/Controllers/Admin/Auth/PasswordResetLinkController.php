<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Enums\NotificationType;
use App\Http\Controllers\Controller;
use App\Notifications\UserAutoNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('admin.pages.auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::broker('admins')->sendResetLink($request->only('email'), function ($user, $token) {
            $link = url(route('admin.password.reset', ['token' => $token, 'email' => $user->email], false));
            $tag_with_link = "<a href='$link' target='_blank' style='display: inline-block;padding: 12px 25px;background-color: #4CAF50;color: white;text-decoration: none;border-radius: 5px;font-weight: bold;margin-top: 15px;'>Reset Password</a>";
            Notification::route('mail', $user->email)->notifyNow(new UserAutoNotification(NotificationType::PASSWORD_RESET, ['reset_link' => $tag_with_link]));
        });

        $message = match ($status) {
            Password::RESET_LINK_SENT => __('Successfully sent password reset link, please check your email.'),
            default => __('Unable to send password reset link'),
        };

        return $status == Password::RESET_LINK_SENT
                    ? back()->withSuccess(__($message))
                    : back()->withInput($request->only('email'))
                        ->withErrors(__($message));
    }
}
