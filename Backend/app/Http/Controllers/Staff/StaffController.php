<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Support\Facades\Storage;
use App\Models\Admin;

class StaffController extends Controller
{
    //Create a new staff
    public function createStaff(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'role' => 'nullable|string', // Role default is 1, but can be specified
            'password' => 'required|string|min:8', // Password should be at least 8 characters
            'nid' => 'required|string',
            'staff_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5048', // Validate image type and size
            'address' => 'required|string',
            'branch_id' => 'required|string',
            'phone' => 'required|string|min:11|max:11',
        ]);

        // Handle the file upload
        $imagePath = $request->file('staff_image')->store('staff', 'public'); // Save in storage/app/public/staff

        // Create the staff
        $staff = Staff::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => '1', // Default role to 1
            'password' => $request->password, // No hash applied as per your request
            'nid' => $request->nid,
            'staff_image' => $imagePath, // Save the file path
            'status' => '1', // Default status is 1
            'branch_id' => $request->branch_id,
            'admin_id' => $request->user()->id, // Use the logged-in admin's ID
            'address' => $request->address,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Staff created successfully',
            'staff' => $staff,
        ], 201);
    }



    // Show all staff for the logged-in admin
    public function showAllStaff(Request $request)
    {
        try {
            // Retrieve all staff records associated with the logged-in admin
            $staff = Staff::where('admin_id', $request->user()->id)->paginate(10);

            // Return the staff data in the response
            return response()->json([
                'success' => true,
                'staff' => $staff,
            ]);
        } catch (\Exception $e) {
            // Catch any errors and return a response with the error message
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving staff data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Edit staff
    public function editStaff($id)
    {
        try {
            // Find the staff member by ID
            $staff = Staff::find($id);

            // Check if the staff member exists
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'staff' => $staff,
            ]);
        } catch (\Exception $e) {
            // Catch any errors and return an error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the staff details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // update staff
    public function updateStaff(Request $request, $id)
    {
        try {
            // Find the staff by ID
            $staff = Staff::find($id);

            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found',
                ], 404);
            }

            // Validate the request data
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:staff,email,' . $staff->id,
                'phone' => 'required|string|min:11|max:11',
                'role' => 'nullable|string',
                'password' => 'nullable|string|min:8', // Optional password update
                'nid' => 'required|string',
                'staff_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image type and size
                'address' => 'required|string',
                'branch_id' => 'required|string',
            ]);

            // If an image is uploaded, handle the file upload
            if ($request->hasFile('staff_image')) {
                // Delete the old image if exists
                if ($staff->staff_image && file_exists(storage_path('app/public/' . $staff->staff_image))) {
                    unlink(storage_path('app/public/' . $staff->staff_image));
                }

                // Store the new image
                $imagePath = $request->file('staff_image')->store('staff', 'public'); // Save in storage/app/public/staff
            } else {
                $imagePath = $staff->staff_image; // Keep the old image if no new image is uploaded
            }

            // Update the staff details
            $staff->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role ?? $staff->role,
                'password' => $request->password ?? $staff->password, // No hash applied
                'nid' => $request->nid,
                'staff_image' => $imagePath, // Save the file path
                'address' => $request->address,
                'branch_id' => $request->branch_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Staff updated successfully',
                'staff' => $staff,
            ]);
        } catch (\Exception $e) {
            // Catch any errors that occur and return an error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating staff details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Delete staff
    public function deleteStaff($id)
    {
        try {
            // Find the staff by ID
            $staff = Staff::find($id);

            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found',
                ], 404);
            }

            // Delete the staff
            $staff->delete();

            return response()->json([
                'success' => true,
                'message' => 'Staff deleted successfully',
            ]);
        } catch (\Exception $e) {
            // Catch any errors that occur and return an error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the staff.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Status change of staff
    public function changeStaffStatus($id)
    {
        try {
            // Find the staff member by ID
            $staff = Staff::find($id);

            // Check if the staff member exists
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found',
                ], 404);
            }

            // Toggle status between 1 and 0
            $staff->status = $staff->status == '1' ? '0' : '1';
            $staff->save();

            return response()->json([
                'success' => true,
                'message' => 'Staff status updated successfully',
                'staff' => $staff,
            ]);
        } catch (\Exception $e) {
            // Catch any errors and return an error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the staff status.',
                'error' => $e->getMessage(),
            ], 500);
        }
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
