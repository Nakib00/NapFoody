<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // create a new product
    public function createProduct(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|string',
                'product_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5048',
            ]);

            // Handle the image upload
            if ($request->hasFile('product_image')) {
                $imagePath = $request->file('product_image')->store('public/products');
                $imageName = str_replace('public/', '', $imagePath);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Product image is required.',
                ], 422);
            }

            // Create a new product
            $product = product::create([
                'name' => $request->name,
                'price' => $request->price,
                'status' => '1', // Default status is 1
                'admin_id' => $request->user()->id,
                'product_image' => $imageName, // Save the formatted path
            ]);

            // Return the created product as a response
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'product' => $product,
            ], 201);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the product.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Edit a product
    public function editProduct($id)
    {
        try {
            // Find the product by ID
            $product = Product::find($id);

            // Check if the product exists
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.',
                ], 404);
            }

            // Return the product details
            return response()->json([
                'success' => true,
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the product details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    // Product update
    public function updateProduct(Request $request, $id)
    {
        try {
            // Find the product by ID
            $product = product::find($id);

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
                // Store the new image and get the path
                $imagePath = $request->file('product_image')->store('public/products');
                $imageName = str_replace('public/', '', $imagePath); // Store path without 'public/'

                // Delete the old image if it exists
                if ($product->product_image) {
                    Storage::delete('public/' . $product->product_image);
                }

                $product->product_image = $imageName; // Update the image name
            }

            // Update the product details
            $product->name = $request->name;
            $product->price = $request->price;
            $product->save();

            // Return the updated product as a response
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the product.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Delete a product
    public function deleteProduct($id)
    {
        try {
            // Find the product by ID
            $product = product::find($id);

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
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the product.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Products status change
    public function toggleProductStatus($id)
    {
        try {
            // Find the product by ID
            $product = product::find($id);

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
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the product status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Show all products
    public function showAllProducts(Request $request)
    {
        try {
            // Get all products associated with the logged-in admin
            $products = product::where('admin_id', $request->user()->id)->get();

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
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the products.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
