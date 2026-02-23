<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CardType;
use Illuminate\Http\Request;

class CardTypeController extends Controller
{
    public function index()
    {
        $cardTypes = CardType::withCount('subcategories')->get();
        return response()->json([
            'status' => true,
            'data' => $cardTypes
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:card_types',
            'description' => 'nullable|string',
        ]);

        $cardType = CardType::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Card type created successfully',
            'data' => $cardType
        ], 201);
    }

    public function show(CardType $cardType)
    {
        $cardType->load('subcategories');
        return response()->json([
            'status' => true,
            'data' => $cardType
        ]);
    }

    public function update(Request $request, CardType $cardType)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:card_types,name,' . $cardType->id,
            'description' => 'nullable|string',
        ]);

        $cardType->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Card type updated successfully',
            'data' => $cardType
        ]);
    }

    public function destroy(CardType $cardType)
    {
        $cardType->delete();

        return response()->json([
            'status' => true,
            'message' => 'Card type deleted successfully'
        ]);
    }
}
