<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Superadmin;
use App\Models\Admin;
use App\Models\Staff;

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


    // Admin login
    public function adminLogin(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find the admin by email
        $admin = Admin::where('email', $request->email)->first();

        // Check if admin exists
        if ($admin) {
            // Check if admin's status is active (1)
            if ($admin->status == 1) {
                // Check if the password matches
                if ($admin->password === $request->password) {
                    // Generate a token using Laravel Sanctum
                    $token = $admin->createToken('admin-token')->plainTextToken;

                    // Return the token and admin details
                    return response()->json([
                        'success' => true,
                        'message' => 'Admin Login successful',
                        'token' => $token,
                        'admin' => $admin,
                        'status' => true,
                    ]);
                } else {
                    // Return an error response if password is incorrect
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid email or password',
                        'status' => false,
                    ], 401);
                }
            } else {
                // Return an error response if status is not active
                return response()->json([
                    'success' => false,
                    'message' => 'Your subscription is over. Contact the authority.',
                    'status' => false,
                ], 403);
            }
        } else {
            // Return an error response if admin is not found
            return response()->json([
                'success' => false,
                'message' => 'Admin not found.',
                'status' => false,
            ], 404);
        }
    }

    //Admin logout
    public function adminlogout(Request $request)
    {
        // Revoke the current user's token
        $request->user()->currentAccessToken()->delete();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Admin Logout successful',
        ]);
    }

    // Staff login
    public function staffLogin(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Check if the staff exists by email
        $staff = Staff::where('email', $request->email)->first();

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff not found',
            ], 404);
        }

        // Check if the admin status is active (1)
        $admin = Admin::find($staff->admin_id); // Assuming you have an Admin model related to the staff's admin
        if (!$admin || $admin->status == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Your subscription is over. Contact the authority.',
            ], 403);
        }

        // Check if the staff's status is active (1)
        if ($staff->status == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Your ID is disabled.',
            ], 403);
        }

        // Check if the password matches (no hashing used)
        if ($staff->password !== $request->password) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Generate token for the logged-in staff
        $token = $staff->createToken('Staff Access Token')->plainTextToken;

        // Return the success response with the token
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'staff' => $staff,
        ], 200);
    }
    // Staff logout
    public function stafflogout(Request $request)
    {
        // Revoke the user's current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff logged out successfully.',
        ], 200);
    }
}
