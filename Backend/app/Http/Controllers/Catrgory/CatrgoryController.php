<?php

namespace App\Http\Controllers\Catrgory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\category;
use Illuminate\Support\Facades\Storage;

class CatrgoryController extends Controller
{
    //Create a new category
    public function categorystore(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'name' => 'required|string|max:255',
                'category_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5048',
            ]);

            // Handle the image upload
            if ($request->hasFile('category_image')) {
                $imagePath = $request->file('category_image')->store('public/category');
                $imageName = str_replace('public/', '', $imagePath); // Store path without 'public/'
            } else {
                return response()->json(['error' => 'Category image is required'], 422);
            }

            // Create the category
            $category = category::create([
                'name' => $request->name,
                'status' => '1',
                'admin_id' => $request->user()->id,
                'category_image' => $imageName,
            ]);

            // Return the created category as a response
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'category' => $category,
            ], 201);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    // Category Edit
    public function editCategory($id)
    {
        try {
            // Find the category by ID
            $category = category::find($id);

            // Check if the category exists
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found.',
                ], 404);
            }

            // Return the category details
            return response()->json([
                'success' => true,
                'category' => $category,
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Category Update
    public function updateCategory(Request $request, $id)
    {
        try {
            // Validate the request data
            $request->validate([
                'name' => 'required|string|max:255',
                'category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Find the category
            $category = category::find($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found.',
                ], 404);
            }

            // Handle the image upload if a new image is provided
            if ($request->hasFile('category_image')) {
                $imagePath = $request->file('category_image')->store('public/category');
                $imageName = str_replace('public/', '', $imagePath);

                // Delete the old image if it exists
                if ($category->category_image) {
                    Storage::delete('public/' . $category->category_image);
                }

                $category->category_image = $imageName;
            }

            // Update category details
            $category->name = $request->name;
            $category->save();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'category' => $category,
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Delete category
    public function deleteCategory($id)
    {
        try {
            // Find the category
            $category = category::find($id);

            // Check if the category exists
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found.',
                ], 404);
            }

            // Delete the image file if it exists
            if ($category->category_image) {
                Storage::delete('public/' . $category->category_image);
            }

            // Delete the category
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.',
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // show all categories
    public function showAllCategories(Request $request)
    {
        try {
            // Get all categories where the admin_id matches the logged-in admin's ID
            $categories = category::where('admin_id', $request->user()->id)->get();

            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No categories found for this admin.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching categories.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    // Category status change
    public function toggleCategoryStatus($id)
    {
        try {
            // Find the category by ID
            $category = category::find($id);

            // Check if the category exists
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found.',
                ], 404);
            }

            // Toggle the status (1 becomes 0, 0 becomes 1)
            $category->status = $category->status == 1 ? 0 : 1;

            // Save the updated category
            $category->save();

            return response()->json([
                'success' => true,
                'message' => 'Category status updated successfully.',
                'category' => $category,
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the category status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
