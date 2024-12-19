<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;

class ManagerController extends Controller
{
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
}
