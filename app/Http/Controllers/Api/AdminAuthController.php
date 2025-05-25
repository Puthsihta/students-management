<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    // Signup with 4-digit verification code
    public function signup(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'name'     => 'required|string|max:255',
                    'email'    => 'required|email|unique:admins,email',
                    'password' => 'required|string|min:6|confirmed',
                ],
                [
                    'name.required'     => 'The name field is required.',

                    'email.required'    => 'The email field is required.',
                    'email.email'       => 'The email field must be a valid email address, e.g. "email@example.com".',
                    'email.unique'      => 'This email is already used by another admin.',

                    'password.required' => 'The password field is required.',
                    'password.string'   => 'The password must be a valid string.',
                    'password.min'      => 'The password must be at least 6 characters.',
                    'password.confirmed' => 'The password confirmation does not match.',
                ]
            );


            $code = random_int(1000, 9999);

            $admin = Admin::create([
                'name'                    => $validated['name'],
                'email'                   => $validated['email'],
                'password'                => Hash::make($validated['password']),
                'email_verification_code' => "$code",
            ]);

            Mail::raw("Your verification code is: $code", function ($message) use ($admin) {
                $message->to($admin->email)
                    ->subject('Email Verification Code');
            });

            return response()->json([
                'message' => 'Signup successful. A 4-digit verification code has been sent to your email.',
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Signup error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 500);
        }
    }

    // Email verification
    public function verifyCode(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|exists:admins,email',
                'code'  => 'required|digits:4',
            ], [
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address, e.g. "admin@example.com".',
                'email.exists' => 'No admin found with this email address.',

                'code.required' => 'The verification code is required.',
                'code.digits' => 'The verification code must be exactly 4 digits.',
            ]);

            $admin = Admin::where('email', $validated['email'])->first();

            if ($admin->email_verification_code !== $validated['code']) {
                return response()->json(['message' => 'Invalid verification code.'], 422);
            }

            $admin->email_verified_at = now();
            $admin->email_verification_code = null;
            $admin->save();

            return response()->json(['message' => 'Email verified successfully.']);
        } catch (ValidationException $e) {
            Log::error('Verification error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to verify email. Please try again.',
                'errors' => $e->errors()
            ], 500);
        }
    }

    // Signin (requires verified email)
    public function signin(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            $admin = Admin::where('email', $credentials['email'])->first();

            if (! $admin || ! Hash::check($credentials['password'], $admin->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            if (! $admin->email_verified_at) {
                return response()->json(['message' => 'Please verify your email before logging in.'], 403);
            }

            $token = $admin->createToken('admin_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful.',
                'token'   => $token,
                'data'   => $admin,
            ]);
        } catch (ValidationException $e) {
            Log::error('Signin error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to sign in. Please try again.',
                'errors' => $e->errors()
            ], 500);
        }
    }

    // Logout
    public function logout(Request $request)
    {
        try {

            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'message' => 'Authorization token is missing. Please include it in the Authorization header as: Bearer YOUR_TOKEN_HERE'
                ], 401);
            }

            // Validate if the user is authenticated
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Invalid or expired token. Please log in again.'
                ], 401);
            }
            // Delete the token
            $user->currentAccessToken()->delete();

            return response()->json(['message' => 'Logged out successfully.']);
        } catch (\Throwable $e) {
            Log::error('Logout error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Logout failed. Please try again.'
            ], 500);
        }
    }
}
