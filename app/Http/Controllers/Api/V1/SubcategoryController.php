<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use App\Models\CardType;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function index(CardType $cardType)
    {
        $subcategories = $cardType->subcategories()->withCount('inventoryItems')->get();
        return response()->json([
            'status' => true,
            'data' => $subcategories
        ]);
    }

    public function store(Request $request, CardType $cardType)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $validated['card_type_id'] = $cardType->id;
        $subcategory = Subcategory::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Subcategory created successfully',
            'data' => $subcategory
        ], 201);
    }

    public function show(CardType $cardType, Subcategory $subcategory)
    {
        $subcategory->load('inventoryItems');
        return response()->json([
            'status' => true,
            'data' => $subcategory
        ]);
    }

    public function update(Request $request, CardType $cardType, Subcategory $subcategory)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $subcategory->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Subcategory updated successfully',
            'data' => $subcategory
        ]);
    }

    public function destroy(CardType $cardType, Subcategory $subcategory)
    {
        $subcategory->delete();

        return response()->json([
            'status' => true,
            'message' => 'Subcategory deleted successfully'
        ]);
    }
}
