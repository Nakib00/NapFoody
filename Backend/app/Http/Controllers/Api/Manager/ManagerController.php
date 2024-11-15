<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\category;
use Illuminate\Support\Facades\Storage;

class ManagerController extends Controller
{
    //Create a new category
    public function categorystore(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'category_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle the image upload
        if ($request->hasFile('category_image')) {
            $imagePath = $request->file('category_image')->store('public/category');
            $imageName = str_replace('public/', '', $imagePath); // Store path without 'public/'
        } else {
            return response()->json(['error' => 'Category image is required'], 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'status' => '1',
            'admin_id' => $request->user()->id,
            'category_image' => $imageName, // Save the formatted path
        ]);

        // Return the created category as a response
        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'category' => $category,
        ], 201);
    }
    // Category Edit
    public function editCategory($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }
    // Category Update
    public function updateCategory(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Find the category
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        // Handle the image upload if a new image is provided
        if ($request->hasFile('category_image')) {
            $imagePath = $request->file('category_image')->store('public/category');
            $imageName = str_replace('public/', '', $imagePath);

            // Delete the old image (optional, if needed)
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
            'message' => 'Category updated successfully',
            'category' => $category,
        ]);
    }
    // Delete category
    public function deleteCategory($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
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
            'message' => 'Category deleted successfully',
        ]);
    }
    // show all categories
    public function showAllCategories(Request $request)
    {
        // Get all categories where the admin_id matches the logged-in admin's ID
        $categories = Category::where('admin_id', $request->user()->id)->get();

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
    }
    // Category status change
    public function toggleCategoryStatus($id)
    {
        // Find the category by ID
        $category = Category::find($id);

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
    }
}
