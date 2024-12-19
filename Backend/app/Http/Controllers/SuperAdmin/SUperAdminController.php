<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Superadmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SUperAdminController extends Controller
{
    // Superadmin Signup
    public function superadminSignup(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            // Catch any errors and return an error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Superadmin Login
    public function superadminLogin(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            // Catch any errors and return an error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Logout function for Superadmin
    public function logout(Request $request)
    {
        try {
            // Revoke the user's current token
            $request->user()->currentAccessToken()->delete();

            $response = [
                'success' => true,
                'message' => 'User logged out successfully',
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            // Catch any errors and return an error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during logout',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Show all admins
    public function index(Request $request)
    {
        try {
            // Ensure the authenticated user is a superadmin
            if ($request->user() && $request->user() instanceof \App\Models\Superadmin) {
                // Fetch all admins
                $admins = Admin::all();

                return response()->json([
                    'message' => 'Admin list retrieved successfully',
                    'admins' => $admins,
                ], 200);
            }

            return response()->json(['message' => 'Unauthorized'], 403);
        } catch (\Exception $e) {
            // Catch any exceptions and return a 500 error with the exception message
            return response()->json([
                'message' => 'An error occurred while retrieving the admin list',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Create a new admin
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
            'nid' => 'required|string',
            'admin_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle the image upload
        if ($request->hasFile('admin_image')) {
            $imagePath = $request->file('admin_image')->store('public/admin');
            $imageName = str_replace('public/', '', $imagePath); // Store path without 'public/'
        } else {
            return response()->json(['error' => 'Admin image is required'], 422);
        }

        // Create the admin without hashing the password
        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'nid' => $request->nid,
            'admin_image' => $imageName,
            'address' => $request->address,
            'status' => '0', // Default status to 0
            'sms_count' => '0',
        ]);

        return response()->json([
            'message' => 'Admin registered successfully',
            'admin' => $admin,
        ], 201);
    }


    // Delete admin
    public function destroy($id)
    {
        try {
            $admin = Admin::find($id);

            if (!$admin) {
                return response()->json(['error' => 'Admin not found'], 404);
            }

            // Delete the admin from the database
            $admin->delete();

            return response()->json(['message' => 'Admin deleted successfully'], 200);
        } catch (\Exception $e) {
            // Catch any exception and return error message
            return response()->json([
                'error' => 'An error occurred while deleting the admin',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // Edit admin
    public function edit($id)
    {
        try {
            $admin = Admin::find($id);

            if (!$admin) {
                return response()->json(['error' => 'Admin not found'], 404);
            }

            return response()->json([
                'admin' => $admin,
            ], 200);
        } catch (\Exception $e) {
            // Catch any exceptions and return a 500 error with the exception message
            return response()->json([
                'error' => 'An error occurred while retrieving the admin',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // Update admin
    public function update(Request $request, $id)
    {
        try {
            $admin = Admin::find($id);

            if (!$admin) {
                return response()->json(['error' => 'Admin not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:admins,email,' . $id,
                'password' => 'sometimes|string|min:6',
                'phone' => 'sometimes|string',
                'nid' => 'sometimes|string',
                'admin_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'address' => 'sometimes|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Update the image if a new one is uploaded
            if ($request->hasFile('admin_image')) {
                // Delete the old image if it exists
                if ($admin->admin_image && Storage::exists('public/' . $admin->admin_image)) {
                    Storage::delete('public/' . $admin->admin_image);
                }

                // Store the new image
                $imagePath = $request->file('admin_image')->store('public/admin');
                $admin->admin_image = str_replace('public/', '', $imagePath);
            }

            // Update fields individually
            $admin->name = $request->input('name', $admin->name);
            $admin->email = $request->input('email', $admin->email);
            if ($request->has('password')) {
                $admin->password = $request->password; // Optional password update
            }
            $admin->phone = $request->input('phone', $admin->phone);
            $admin->nid = $request->input('nid', $admin->nid);
            $admin->address = $request->input('address', $admin->address);

            // Save the updated model
            $admin->save();

            return response()->json([
                'message' => 'Admin updated successfully',
                'admin' => $admin,
            ], 200);
        } catch (\Exception $e) {
            // Catch any exception and return error message
            return response()->json([
                'error' => 'An error occurred while updating the admin',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // Toggle status method
    public function toggleStatus($id)
    {
        try {
            $admin = Admin::find($id);

            if (!$admin) {
                return response()->json(['error' => 'Admin not found'], 404);
            }

            // Toggle status (0 to 1, or 1 to 0)
            $admin->status = $admin->status == '1' ? '0' : '1';
            $admin->save();

            return response()->json([
                'message' => 'Admin status updated successfully',
                'status' => $admin->status,
            ], 200);
        } catch (\Exception $e) {
            // Catch any exceptions and return a 500 error with the exception message
            return response()->json([
                'error' => 'An error occurred while updating the status',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // Method to add SMS count
    public function addSmsCount(Request $request, $id)
    {
        try {
            $admin = Admin::find($id);

            if (!$admin) {
                return response()->json(['error' => 'Admin not found'], 404);
            }

            // Validate the input
            $request->validate([
                'sms_count' => 'required|integer|min:1',
            ]);

            // Add the new SMS count to the existing sms_count
            $admin->sms_count += $request->sms_count;

            // Save changes to the database
            $admin->save();

            return response()->json([
                'message' => 'SMS count updated successfully',
                'sms_count' => $admin->sms_count,
            ], 200);
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error updating SMS count: ' . $e->getMessage());

            return response()->json(['error' => 'Failed to update SMS count'], 500);
        }
    }


    // Method to Remove SMS count
    public function removeSmsCount(Request $request, $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['error' => 'Admin not found'], 404);
        }

        // Validate the input
        $request->validate([
            'sms_count' => 'required|integer',
        ]);

        // Add the new SMS count to the existing sms_count
        $admin->sms_count = $admin->sms_count - $request->sms_count;
        $admin->save();

        return response()->json([
            'message' => 'SMS count updated successfully',
            'sms_count' => $admin->sms_count,
        ], 200);
    }
}
