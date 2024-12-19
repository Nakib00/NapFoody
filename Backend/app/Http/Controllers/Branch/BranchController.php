<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\branch;
use Illuminate\Support\Facades\Storage;

class BranchController extends Controller
{
    // Branch create
    public function createBranch(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'name' => 'required|string|max:255',
                'branch_code' => 'required|string|max:5',
                'branch_phone' => 'required|string|min:11|max:11',
                'branch_address' => 'required|string',
            ]);

            // Create the branch
            $branch = branch::create([
                'name' => $request->name,
                'branch_code' => $request->branch_code,
                'branch_phone' => $request->branch_phone,
                'branch_address' => $request->branch_address,
                'admin_id' => $request->user()->id,
            ]);

            // Return the created branch
            return response()->json([
                'success' => true,
                'message' => 'Branch created successfully',
                'branch' => $branch,
            ], 201);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the branch.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Edit a branch
    public function editBranch($id)
    {
        try {
            // Find the branch by ID for the logged-in admin
            $branch = branch::where('admin_id', auth()->user()->id)->find($id);

            if (!$branch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch not found or you do not have permission to view it.',
                ], 404);
            }

            // Return the branch details
            return response()->json([
                'success' => true,
                'branch' => $branch,
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the branch.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // update branch
    public function updateBranch(Request $request, $id)
    {
        try {
            // Validate the request data
            $request->validate([
                'name' => 'required|string|max:255',
                'branch_code' => 'required|string|max:5',
                'branch_phone' => 'required|string|min:11|max:11',
                'branch_address' => 'required|string',
            ]);

            // Find the branch by ID for the logged-in admin
            $branch = branch::where('admin_id', auth()->user()->id)->find($id);

            if (!$branch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch not found or you do not have permission to edit it.',
                ], 404);
            }

            // Update the branch
            $branch->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Branch updated successfully',
                'branch' => $branch,
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the branch.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Delete a branch
    public function deleteBranch($id)
    {
        try {
            // Find the branch by ID for the logged-in admin
            $branch = branch::where('admin_id', auth()->user()->id)->find($id);

            if (!$branch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch not found or you do not have permission to delete it.',
                ], 404);
            }

            // Delete the branch
            $branch->delete();

            return response()->json([
                'success' => true,
                'message' => 'Branch deleted successfully',
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the branch.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    // Show all branch for that login admin
    public function showAllBranches(Request $request)
    {
        try {
            // Get all branches associated with the logged-in admin
            $branches = branch::where('admin_id', $request->user()->id)->get();

            // Check if branches are found
            if ($branches->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No branches found for this admin.',
                ], 404);
            }

            // Return the list of branches
            return response()->json([
                'success' => true,
                'branches' => $branches,
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving branches.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
