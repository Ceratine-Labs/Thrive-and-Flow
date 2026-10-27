<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Those details don\'t match our records — try again?']);
        }

        $request->session()->regenerate();

        return redirect()->intended(
            Auth::user()->isAdmin() ? route('admin.dashboard') : route('studio.dashboard')
        );
    }

    public function showRegister(Request $request): View
    {
        return view('auth.register', ['plan' => $request->query('plan')]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique(User::class, 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'stage' => ['required', Rule::in(['pregnant', 'postpartum', 'browsing'])],
            'due_date' => ['exclude_unless:stage,pregnant', 'required', 'date', 'after:today', 'before:+10 months'],
            'baby_born_at' => ['exclude_unless:stage,postpartum', 'required', 'date', 'before_or_equal:today', 'after:-3 years'],
            'plan' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'member',
            'due_date' => $data['due_date'] ?? null,
            'baby_born_at' => $data['baby_born_at'] ?? null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        if (! empty($data['plan'])) {
            return redirect()->route('pricing')->with('autoplan', $data['plan']);
        }

        return redirect()->route('pricing')
            ->with('status', "Welcome, {$user->firstName()} — pick a plan to open the studio. Your first week is free.");
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
