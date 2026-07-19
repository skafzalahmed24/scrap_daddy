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

        if ($credentials['email'] === 'admin@scrapedaddy.com' && $credentials['password'] === 'Vzario@123') {
            return response()->json(['success' => true, 'redirect' => '/dashboard']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
    }
}
