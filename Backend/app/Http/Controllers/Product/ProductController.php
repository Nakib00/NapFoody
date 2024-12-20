<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\product;
use App\Models\size;
use App\Models\extra;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // create a new product
    public function createProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'product_image' => 'required|image|mimes:jpg,jpeg,png|max:4048',
            'sizes' => 'required|array',
            'sizes.*.name' => 'required|string|max:255',
            'sizes.*.price' => 'required|numeric',
            'extras' => 'nullable|array',
            'extras.*.name' => 'nullable|string|max:255',
            'extras.*.price' => 'nullable|numeric',
        ]);

        try {
            // Save product image
            $imagePath = $request->file('product_image')->store('public/product');
            $imageName = str_replace('public/', '', $imagePath);

            // Save product
            $product = product::create([
                'name' => $request->name,
                'status' => 1,
                'admin_id' => auth()->id(),
                'product_image' => $imageName,
            ]);

            // Save sizes
            foreach ($request->sizes as $size) {
                size::create([
                    'name' => $size['name'],
                    'product_id' => $product->id,
                    'price' => $size['price'],
                ]);
            }

            // Save extras
            if (!empty($request->extras)) {
                foreach ($request->extras as $extra) {
                    extra::create([
                        'name' => $extra['name'] ?? null,
                        'product_id' => $product->id,
                        'price' => $extra['price'] ?? null,
                    ]);
                }
            }

            return response()->json([
                'message' => 'Product created successfully.',
                'product' => $product,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create product.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Edit a product
    public function editProduct($id)
    {
        try {
            // Find the product by ID with its related sizes and extras
            $product = product::with(['sizes', 'extras'])->find($id);

            // Check if the product exists
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.',
                ], 404);
            }

            // Return the product details with related data
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

            // Delete related sizes and extras
            $product->sizes()->delete();
            $product->extras()->delete();

            // Delete the product
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product and its related data deleted successfully.',
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
            $products = product::where('admin_id', $request->user()->id)
                ->with(['sizeRegular' => function ($query) {
                    $query->where('name', 'Regular')->select('product_id', 'price');
                }])
                ->get();

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

    // product size function
    public function addSize(Request $request, $productId)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
        ]);

        try {
            // Add the size
            $size = new size();
            $size->name = $validated['name'];
            $size->price = $validated['price'];
            $size->product_id = $productId;
            $size->save();

            return response()->json([
                'success' => true,
                'message' => 'Size added successfully.',
                'size' => $size,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the size.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function editSize($id)
    {
        try {
            // Find the size by ID
            $size = size::find($id);

            if (!$size) {
                return response()->json([
                    'success' => false,
                    'message' => 'Size not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'size' => $size,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the size details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateSize(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
        ]);

        try {
            // Find the size by ID
            $size = size::find($id);

            if (!$size) {
                return response()->json([
                    'success' => false,
                    'message' => 'Size not found.',
                ], 404);
            }

            // Update the size details
            $size->name = $validated['name'];
            $size->price = $validated['price'];
            $size->save();

            return response()->json([
                'success' => true,
                'message' => 'Size updated successfully.',
                'size' => $size,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the size.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteSize($id)
    {
        try {
            // Find the size by ID
            $size = Size::find($id);

            if (!$size) {
                return response()->json([
                    'success' => false,
                    'message' => 'Size not found.',
                ], 404);
            }

            // Delete the size
            $size->delete();

            return response()->json([
                'success' => true,
                'message' => 'Size deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the size.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function addExtra(Request $request, $productId)
    {
        $validated = $request->validate([
            'name' => 'nullable|string',
            'price' => 'nullable|numeric',
        ]);

        try {
            // Add the extra
            $extra = new extra();
            $extra->name = $validated['name'];
            $extra->price = $validated['price'];
            $extra->product_id = $productId;
            $extra->save();

            return response()->json([
                'success' => true,
                'message' => 'Extra added successfully.',
                'extra' => $extra,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the extra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function editExtra($id)
    {
        try {
            // Find the extra by ID
            $extra = extra::find($id);

            if (!$extra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Extra not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'extra' => $extra,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the extra details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateExtra(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string',
            'price' => 'nullable|numeric',
        ]);

        try {
            // Find the extra by ID
            $extra = extra::find($id);

            if (!$extra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Extra not found.',
                ], 404);
            }

            // Update the extra details
            $extra->name = $validated['name'];
            $extra->price = $validated['price'];
            $extra->save();

            return response()->json([
                'success' => true,
                'message' => 'Extra updated successfully.',
                'extra' => $extra,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the extra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteExtra($id)
    {
        try {
            // Find the extra by ID
            $extra = extra::find($id);

            if (!$extra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Extra not found.',
                ], 404);
            }

            // Delete the extra
            $extra->delete();

            return response()->json([
                'success' => true,
                'message' => 'Extra deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the extra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
