<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function forgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function requestPasswordReset(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $username = strtolower(trim($validated['username']));
        $email = strtolower(trim($validated['email']));

        $targetUser = User::query()
            ->whereRaw('LOWER(username) = ?', [$username])
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (! $targetUser) {
            return back()
                ->withErrors([
                    'username' => 'No user matches the provided username and email.',
                ])
                ->withInput();
        }

        PasswordResetRequest::query()->updateOrCreate(
            [
                'user_id' => $targetUser->id,
                'status' => PasswordResetRequest::STATUS_PENDING,
            ],
            [
                'requested_username' => $targetUser->username ?? $validated['username'],
                'requested_email' => $targetUser->email,
                'requested_at' => now(),
                'notes' => null,
            ]
        );

        return back()->with('success', 'Password reset request submitted. Please wait for admin approval.');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required_without:email', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $login = strtolower(trim((string) ($credentials['login'] ?? $credentials['email'] ?? '')));
        $remember = (bool) ($credentials['remember'] ?? false);
        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$login])
            ->orWhereRaw('LOWER(username) = ?', [$login])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors([
                    'login' => 'The provided credentials do not match our records.',
                ])
                ->onlyInput('login');
        }

        Auth::login($user, $remember);

        $request->session()->regenerate();

        $user = $request->user();

        if (! $user->is_admin && ! $user->hasActiveSubscription()) {
            return redirect()
                ->route('subscription.plans')
                ->with('warning', 'Your subscription has expired. Please renew to continue.');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'username' => ['nullable', 'string', 'min:3', 'max:40', 'alpha_dash', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => false,
            'is_worker' => false,
        ]);

        $user->forceFill(['account_owner_id' => $user->id])->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('subscription.plans')
            ->with('success', 'Account created successfully. Choose a subscription plan to continue.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
