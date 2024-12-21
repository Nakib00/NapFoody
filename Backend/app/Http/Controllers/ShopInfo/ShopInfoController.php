<?php

namespace App\Http\Controllers\ShopInfo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShopInfo;
use Illuminate\Support\Facades\Auth;

class ShopInfoController extends Controller
{
    // store shop information
    public function store(Request $request)
    {
        try {
            // Check if the logged-in user already has shop info
            $existingShopInfo = ShopInfo::where('admin_id', Auth::id())->first();
            if ($existingShopInfo) {
                return response()->json(['message' => 'You already have shop info. Cannot add another.'], 403);
            }

            // Validate incoming data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'websit' => 'nullable|string|max:255',
                'phone' => 'required|string|max:15',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'zip' => 'required|string|max:10',
                'address' => 'required|string',
                'logo' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            ]);

            // Save the image in storage/public/ShopInfo
            $imagePath = $request->file('logo')->store('ShopInfo', 'public');

            // Create shop info
            $shopInfo = ShopInfo::create([
                'name' => $validatedData['name'],
                'admin_id' => Auth::id(),
                'email' => $validatedData['email'],
                'websit' => $validatedData['websit'],
                'phone' => $validatedData['phone'],
                'city' => $validatedData['city'],
                'state' => $validatedData['state'],
                'country' => $validatedData['country'],
                'zip' => $validatedData['zip'],
                'address' => $validatedData['address'],
                'logo' => $imagePath,
            ]);

            return response()->json(['message' => 'Shop info created successfully.', 'data' => $shopInfo], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create shop info.', 'error' => $e->getMessage()], 500);
        }
    }


    // show show information
    public function show()
    {
        try {
            // Retrieve shop info for the authenticated user
            $shopInfo = ShopInfo::where('admin_id', Auth::id())->first();

            if (!$shopInfo) {
                return response()->json(['message' => 'No shop info found for the logged-in user.'], 404);
            }

            return response()->json(['data' => $shopInfo], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve shop info.', 'error' => $e->getMessage()], 500);
        }
    }

    // update shop information
    public function update(Request $request)
    {
        try {
            // Fetch the shop info for the logged-in user
            $shopInfo = ShopInfo::where('admin_id', Auth::id())->first();

            if (!$shopInfo) {
                return response()->json(['message' => 'No shop info found for the logged-in user.'], 404);
            }

            // Validate the incoming data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'websit' => 'nullable|string|max:255',
                'phone' => 'required|string|max:15',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'zip' => 'required|string|max:10',
                'address' => 'required|string',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            ]);

            // Update the logo if provided
            if ($request->hasFile('logo')) {
                $imagePath = $request->file('logo')->store('ShopInfo', 'public');
                $validatedData['logo'] = $imagePath;
            }

            // Update the shop info
            $shopInfo->update($validatedData);

            return response()->json(['message' => 'Shop info updated successfully.', 'data' => $shopInfo], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update shop info.', 'error' => $e->getMessage()], 500);
        }
    }
}
