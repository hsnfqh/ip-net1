<?php
// app/Http/Controllers/PasswordResetController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // ─── Forgot Password ────────────────────────────────────────────────────

    /** Tampilkan form request reset link */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /** Kirim link reset ke email */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.exists'   => 'Email tidak ditemukan di sistem kami.',
        ]);

        // Laravel Password facade: buat token, simpan hash di DB, kirim email
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Link reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam.');
        }

        return back()->withErrors(['email' => __($status)])->withInput();
    }

    // ─── Reset Password ──────────────────────────────────────────────────────

    /** Tampilkan form reset password (dari link email) */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /** Proses reset password */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => ['required'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ], [
            'token.required'                 => 'Token reset tidak valid.',
            'email.required'                 => 'Email wajib diisi.',
            'email.email'                    => 'Format email tidak valid.',
            'password.required'              => 'Password baru wajib diisi.',
            'password.min'                   => 'Password minimal 8 karakter.',
            'password.confirmed'             => 'Konfirmasi password tidak sesuai.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
        ]);

        // Laravel Password facade: verifikasi token, hash password, update user
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('status', 'Password berhasil direset. Silakan masuk dengan password baru Anda.');
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput($request->only('email'));
    }
}
