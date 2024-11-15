<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Support\Facades\Storage;

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
            'staff_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image type and size
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

    // show all staff
    public function showAllStaff(Request $request)
    {
        $staff = Staff::where('admin_id', $request->user()->id)->get();

        return response()->json([
            'success' => true,
            'staff' => $staff,
        ]);
    }
    // Edit staff
    public function editStaff($id)
    {
        $staff = Staff::find($id);

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
    }
    // update staff
    public function updateStaff(Request $request, $id)
    {
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

        // Update the staff
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
    }
    // Delete staff
    public function deleteStaff($id)
    {
        $staff = Staff::find($id);

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff not found',
            ], 404);
        }

        $staff->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff deleted successfully',
        ]);
    }
    // Status change of staff
    public function changeStaffStatus($id)
    {
        $staff = Staff::find($id);

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
    }
}
