<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Order;
use App\Models\StaticPage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
            return response()->json([
                'status' => 0, 
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $otp = '123456';

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
            'otp' => $otp,
            'is_verified' => false,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Log::info("OTP for Registration (User: {$user->phone_number}): {$otp}");

        return response()->json([
            'status' => 1,
            'message' => 'Registration successful. Please verify your OTP to login.',
            'data' => ['user' => $user]
        ], 201);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // phone or email
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0, 
                'message' => 'Validation error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->login)
                    ->orWhere('phone_number', $request->login)
                    ->first();

        if (!$user) {
            return response()->json(['status' => 0, 'message' => 'User not found'], 404);
        }

        if ($user->otp !== $request->otp) {
            return response()->json(['status' => 0, 'message' => 'Invalid OTP'], 400);
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['status' => 0, 'message' => 'OTP has expired'], 400);
        }

        $user->update([
            'is_verified' => true,
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        $accessToken = $user->createToken('auth_token', ['access'])->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', ['refresh'])->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => 'OTP verified successfully.',
            'data' => [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'user' => $user
            ]
        ]);
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
            return response()->json([
                'status' => 0, 
                'message' => 'Validation error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->login)
                    ->orWhere('phone_number', $request->login)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 0, 'message' => 'Invalid login credentials'], 401);
        }

        if (!$user->is_verified) {
            $otp = '123456';
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
            ]);
            Log::info("OTP for Login Verification (User: {$user->phone_number}): {$otp}");
            
            return response()->json([
                'status' => 0,
                'message' => 'Please verify your account first. A new OTP has been sent.',
                'data' => ['requires_verification' => true]
            ], 403);
        }

        if ($request->has('device_id') || $request->has('platform_type')) {
            $user->update([
                'device_id' => $request->device_id ?? $user->device_id,
                'platform_type' => $request->platform_type ?? $user->platform_type,
            ]);
        }

        $accessToken = $user->createToken('auth_token', ['access'])->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', ['refresh'])->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => 'Login successful',
            'data' => [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'user' => $user
            ]
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // phone or email
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0, 
                'message' => 'Validation error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->login)
                    ->orWhere('phone_number', $request->login)
                    ->first();

        if (!$user) {
            return response()->json(['status' => 0, 'message' => 'User not found'], 404);
        }

        $otp = '123456';
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Log::info("OTP for Forgot Password (User: {$user->phone_number}): {$otp}");

        return response()->json([
            'status' => 1,
            'message' => 'OTP sent successfully for password reset.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0, 
                'message' => 'Validation error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->login)
                    ->orWhere('phone_number', $request->login)
                    ->first();

        if (!$user) {
            return response()->json(['status' => 0, 'message' => 'User not found'], 404);
        }

        if ($user->otp !== $request->otp) {
            return response()->json(['status' => 0, 'message' => 'Invalid OTP'], 400);
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['status' => 0, 'message' => 'OTP has expired'], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'otp' => null,
            'otp_expires_at' => null,
            'is_verified' => true, // verify them if they weren't already
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Password reset successfully. You can now login.'
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0, 
                'message' => 'Validation error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['status' => 0, 'message' => 'Old password does not match'], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Password changed successfully.'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'status' => 1,
            'message' => 'User fetched successfully',
            'data' => [
                'user' => $request->user()
            ]
        ]);
    }

    public function refreshToken(Request $request)
    {
        if (!$request->user()->currentAccessToken()->can('refresh')) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid refresh token.'
            ], 403);
        }
        
        $request->user()->currentAccessToken()->delete(); // Revoke current refresh token
        
        $accessToken = $request->user()->createToken('auth_token', ['access'])->plainTextToken;
        $refreshToken = $request->user()->createToken('refresh_token', ['refresh'])->plainTextToken;
        
        return response()->json([
            'status' => 1,
            'message' => 'Tokens refreshed successfully.',
            'data' => [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $request->user()->uuid . ',uuid',
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number,' . $request->user()->uuid . ',uuid',
            'pin_code' => 'nullable|string|max:10',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0, 
                'message' => 'Validation error', 
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $user->update($request->only([
            'full_name', 'email', 'phone_number', 'pin_code', 'location'
        ]));
        
        return response()->json([
            'status' => 1,
            'message' => 'Profile updated successfully!',
            'data' => [
                'user' => $user
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Logged out successfully.'
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Account deleted successfully.'
        ]);
    }

    // --- CATEGORY METHODS ---
    public function categories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string',
            'min' => 'nullable|integer|min:0',
            'max' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Category::where('status', 1);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('min') && is_numeric($request->min)) {
            $query->offset((int)$request->min);
        }
        
        if ($request->has('max') && is_numeric($request->max)) {
            $query->limit((int)$request->max);
        }

        $categories = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 1,
            'message' => 'Categories fetched successfully',
            'data' => $categories
        ]);
    }

    // --- SUBCATEGORY METHODS ---
    public function subcategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_uuid' => 'required|exists:categories,uuid',
            'search' => 'nullable|string',
            'sort' => 'nullable|string|in:asc,desc'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Subcategory::where('category_id', $request->category_uuid)
                            ->where('status', 1);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sort = 'asc';
        if ($request->has('sort') && in_array(strtolower($request->sort), ['asc', 'desc'])) {
            $sort = strtolower($request->sort);
        }

        $subcategories = $query->orderBy('name', $sort)->get();

        return response()->json([
            'status' => 1,
            'message' => 'Subcategories fetched successfully',
            'data' => $subcategories
        ]);
    }

    // --- ORDER METHODS ---
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subcategory_uuid' => 'required|exists:subcategories,uuid',
            'pickup_location' => 'required|string',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required|string',
            'notes' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $subcategory = Subcategory::find($request->subcategory_uuid);

        $order = new Order();
        $order->user_uuid = $request->user()->uuid;
        $order->category_uuid = $subcategory->category_id;
        $order->subcategory_uuid = $request->subcategory_uuid;
        $order->status = 'pending';
        $order->pickup_location = $request->pickup_location;
        $order->pickup_date = $request->pickup_date;
        $order->pickup_time = $request->pickup_time;
        $order->notes = $request->notes;

        $uploadedImages = [];
        if ($request->hasFile('images')) {
            $targetDir = public_path('orders');
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }
            
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                file_put_contents($targetDir . '/' . $imageName, file_get_contents($image->getRealPath()));
                $uploadedImages[] = 'orders/' . $imageName;
            }
        }
        
        $order->images = $uploadedImages;
        $order->save();

        return response()->json([
            'status' => 1,
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }

    public function orders(Request $request)
    {
        $orders = Order::with(['category', 'subcategory'])
                       ->where('user_uuid', $request->user()->uuid)
                       ->orderBy('created_at', 'desc')
                       ->get();

        return response()->json([
            'status' => 1,
            'message' => 'Orders fetched successfully',
            'data' => $orders
        ]);
    }

    public function showOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::with(['category', 'subcategory'])
                      ->where('user_uuid', $request->user()->uuid)
                      ->where('id', $request->order_id)
                      ->first();

        if (!$order) {
            return response()->json(['status' => 0, 'message' => 'Order not found'], 404);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Order details fetched successfully',
            'data' => $order
        ]);
    }

    public function payments(Request $request)
    {
        $orders = Order::where('user_uuid', $request->user()->uuid)
                       ->whereNotNull('payment_id')
                       ->orWhere('payment_status', '!=', 'pending')
                       ->orderBy('created_at', 'desc')
                       ->get(['id', 'total_amount', 'payment_status', 'payment_id', 'created_at']);

        return response()->json([
            'status' => 1,
            'message' => 'Payments fetched successfully',
            'data' => $orders
        ]);
    }

    // --- STATIC PAGE METHODS ---
    public function page(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $type = $request->type;
        $page = StaticPage::where('slug', $type)
                          ->orWhere('title', str_replace('-', ' ', $type))
                          ->first();

        if (!$page) {
            return response()->json(['status' => 0, 'message' => 'Page not found'], 404);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Page fetched successfully',
            'data' => $page
        ]);
    }
}
