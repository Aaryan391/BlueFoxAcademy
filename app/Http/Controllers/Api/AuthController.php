<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|in:admin,teacher,user', // Allow role to be optional // Validate role
        ]);

        // Create user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'] ?? 'user',
        ]);

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login a user.
     */
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find user by email
        $user = User::where('email', $validatedData['email'])->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Generate token
        $tokenResult = $user->createToken('auth_token');
        $token = $tokenResult->plainTextToken;

        // Set token as HTTP-only cookie
        $cookie = cookie('auth_token', $token, 60 * 24); // 1 day expiration

        $redirectTo = match ($user->role) {
            'admin' => '/admindashboard',
            'teacher' => '/teacherdashboard',
            default => '/',
        };

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'redirect' => $redirectTo,
                'token' => $token,
            ],
        ], 200)->cookie($cookie);
    }



    /**
     * Logout the user.
     */
    public function logout(Request $request)
    {
        // Delete all tokens of the logged-in user
        $request->user()->tokens()->delete();

        // Optionally, clear the auth_token cookie as well
        $cookie = cookie('auth_token', '', -1); // Expire the cookie immediately

        return response()->json([
            'message' => 'Logged out successfully',
        ])->cookie($cookie); // Return the expired cookie to remove it
    }
}
