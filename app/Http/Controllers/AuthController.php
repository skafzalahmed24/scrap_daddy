<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $validEmail = hash_equals((string) config('admin.email'), $credentials['email']);
        $validPassword = hash_equals((string) config('admin.password'), $credentials['password']);

        if ($validEmail && $validPassword) {
            $request->session()->regenerate();
            $request->session()->put('is_admin', true);

            return response()->json(['success' => true, 'redirect' => url('/dashboard')]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('is_admin');
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
