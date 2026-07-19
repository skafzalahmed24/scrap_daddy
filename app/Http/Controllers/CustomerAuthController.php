<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:6',
            'pin_code' => 'nullable|string|max:10',
            'location' => 'nullable|string|max:255',
            'device_id' => 'nullable|string|max:255',
            'device_unique_id' => 'nullable|string|max:255',
            'device_details' => 'nullable|string',
            'platform_type' => 'required|integer|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'pin_code' => $request->pin_code,
            'location' => $request->location,
            'device_id' => $request->device_id,
            'device_unique_id' => $request->device_unique_id,
            'device_details' => $request->device_details,
            'platform_type' => $request->platform_type,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // Can be email or phone
            'password' => 'required|string',
            'device_id' => 'nullable|string|max:255',
            'platform_type' => 'nullable|integer|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find user by email or phone
        $user = User::where('email', $request->login)
                    ->orWhere('phone_number', $request->login)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        // Update device info on login if provided
        if ($request->has('device_id') || $request->has('platform_type')) {
            $user->update([
                'device_id' => $request->device_id ?? $user->device_id,
                'platform_type' => $request->platform_type ?? $user->platform_type,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user() ?? User::first();
        if ($user) {
            $user->update([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'pin_code' => $request->pin_code,
                'location' => $request->location,
            ]);
        }
        return back()->with('success', 'Profile updated successfully!');
    }
}
