<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Superadmin;

class AuthController extends Controller
{
    // Superadmin Signup
    public function superadminSignup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:superadmins,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $superadmin = Superadmin::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $superadmin->createToken('superadminToken')->plainTextToken;

        return response()->json([
            'message' => 'Superadmin registered successfully',
            'token' => $token,
            'superadmin' => $superadmin,
        ], 201);
    }

    // Superadmin Login
    public function superadminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $superadmin = Superadmin::where('email', $request->email)->first();

        if (!$superadmin || !Hash::check($request->password, $superadmin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $superadmin->createToken('superadminToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'superadmin' => $superadmin,
        ], 200);
    }
    // Logout function for Superadmin
    public function logout(Request $request)
    {
        // Revoke the user's current token
        $request->user()->currentAccessToken()->delete();

        $response = [
            'success' => true,
            'message' => 'User logged out successfully',
        ];

        return response()->json($response, 200);
    }
}
