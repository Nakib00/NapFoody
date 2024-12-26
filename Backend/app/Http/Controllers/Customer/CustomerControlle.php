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
}
