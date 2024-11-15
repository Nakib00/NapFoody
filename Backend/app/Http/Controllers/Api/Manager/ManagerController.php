<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\category;
use App\Models\product;
use App\Models\Branch;
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

    // Products functions
    // create a new product
    public function createProduct(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|string',
            'product_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle the image upload
        if ($request->hasFile('product_image')) {
            $imagePath = $request->file('product_image')->store('public/products');
            $imageName = str_replace('public/', '', $imagePath); // Store path without 'public/'
        } else {
            return response()->json(['error' => 'Product image is required'], 422);
        }

        // Create a new product
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'status' => '1', // Default status is 1
            'admin_id' => $request->user()->id,
            'product_image' => $imageName, // Save the formatted path
        ]);

        // Return the created product as a response
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product' => $product,
        ], 201);
    }
    // Edit a product
    public function editProduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }
    // Product update
    public function updateProduct(Request $request, $id)
    {
        // Find the product by ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|string',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle the image upload if a new image is provided
        if ($request->hasFile('product_image')) {
            $imagePath = $request->file('product_image')->store('public/products');
            $imageName = str_replace('public/', '', $imagePath); // Store path without 'public/'
            $product->product_image = $imageName; // Update the image name
        }

        // Update product details
        $product->name = $request->name;
        $product->price = $request->price;
        $product->save();

        // Return the updated product
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }
    // Delete a product
    public function deleteProduct($id)
    {
        // Find the product by ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        // Delete the product
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
    // Products status change
    public function toggleProductStatus($id)
    {
        // Find the product by ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        // Toggle the status (1 becomes 0, 0 becomes 1)
        $product->status = $product->status == 1 ? 0 : 1;

        // Save the updated product
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully.',
            'product' => $product,
        ]);
    }
    // Show all products
    public function showAllProducts(Request $request)
    {
        // Get all products associated with the logged-in admin
        $products = Product::where('admin_id', $request->user()->id)->get();

        // Check if products are found
        if ($products->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No products found for this admin.',
            ], 404);
        }

        // Return the list of products
        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    // Branch function
    // Branch create
    public function createBranch(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_code' => 'required|string|max:5',
            'branch_phone' => 'required|string|min:11|max:11',
            'branch_address' => 'required|string',
        ]);

        // Create the branch
        $branch = Branch::create([
            'name' => $request->name,
            'branch_code' => $request->branch_code,
            'branch_phone' => $request->branch_phone,
            'branch_address' => $request->branch_address,
            'admin_id' => $request->user()->id,  // Set admin_id to the logged-in admin's ID
        ]);

        // Return the created branch
        return response()->json([
            'success' => true,
            'message' => 'Branch created successfully',
            'branch' => $branch,
        ], 201);
    }
    // Edit a branch
    public function editBranch($id)
    {
        // Find the branch by ID for the logged-in admin
        $branch = Branch::where('admin_id', auth()->user()->id)->find($id);

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
    }
    // update branch
    public function updateBranch(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_code' => 'required|string|max:5',
            'branch_phone' => 'required|string|min:11|max:11',
            'branch_address' => 'required|string',
        ]);

        // Find the branch by ID for the logged-in admin
        $branch = Branch::where('admin_id', auth()->user()->id)->find($id);

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
    }
    // Delete a branch
    public function deleteBranch($id)
    {
        // Find the branch by ID for the logged-in admin
        $branch = Branch::where('admin_id', auth()->user()->id)->find($id);

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
    }
    // Show all branch for that login admin
    public function showAllBranches(Request $request)
    {
        // Get all branches associated with the logged-in admin
        $branches = Branch::where('admin_id', $request->user()->id)->get();

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
    }
}
