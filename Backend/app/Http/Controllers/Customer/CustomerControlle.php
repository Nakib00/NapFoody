<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Costomer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class CustomerControlle extends Controller
{
    //store the customer
    public function store(Request $request)
    {
        try {
            // Validate the input
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:15|unique:costomers,phone',
            ]);

            // Create a new customer
            $customer = Costomer::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'admin_id' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Customer created successfully.',
                'customer' => $customer,
            ], 201);
        } catch (QueryException $e) {
            // Handle database-related errors
            return response()->json([
                'message' => 'Failed to create customer.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // show all customers
    public function index()
    {
        try {
            // Get all customers for the logged-in admin
            $customers = Costomer::where('admin_id', Auth::id())->get();

            return response()->json([
                'message' => 'Customers retrieved successfully.',
                'customers' => $customers,
            ], 200);
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'message' => 'Failed to retrieve customers.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // shwo customer by id
    public function show($id)
    {
        try {
            $customer = Costomer::where('admin_id', Auth::id())->findOrFail($id);

            return response()->json([
                'message' => 'Customer retrieved successfully.',
                'customer' => $customer,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle the case where the customer is not found
            return response()->json([
                'message' => 'Customer not found.',
            ], 404);
        } catch (\Exception $e) {
            // Handle other general errors
            return response()->json([
                'message' => 'An error occurred while retrieving the customer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // update customer
    public function update(Request $request, $id)
    {
        try {
            // Validate input
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:15|unique:costomers,phone,' . $id,
            ]);

            // Find the customer and ensure it belongs to the logged-in admin
            $customer = Costomer::where('admin_id', Auth::id())->findOrFail($id);

            // Update customer details
            $customer->update($request->only(['name', 'phone']));

            return response()->json([
                'message' => 'Customer updated successfully.',
                'customer' => $customer,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Customer not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the customer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // delete customer
    public function destroy($id)
    {
        try {
            // Find the customer and ensure it belongs to the logged-in admin
            $customer = Costomer::where('admin_id', Auth::id())->findOrFail($id);

            // Delete the customer
            $customer->delete();

            return response()->json([
                'message' => 'Customer deleted successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Customer not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the customer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
