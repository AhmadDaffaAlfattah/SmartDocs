<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required'],
            'password' => ['required'],
        ]);

        // Robust lookup: ignore case and spaces (e.g. "Super Admin" == "superadmin")
        $normalizedInput = strtolower(str_replace(' ', '', $credentials['name']));
        $user = \App\Models\User::whereRaw("LOWER(REPLACE(name, ' ', '')) = ?", [$normalizedInput])->first();

        if ($user && $user->password === $credentials['password']) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'name' => 'The provided credentials do not match our records.',
        ])->onlyInput('name');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
