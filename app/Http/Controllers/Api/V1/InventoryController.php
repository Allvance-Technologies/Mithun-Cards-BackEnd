<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Http\Resources\V1\InventoryResource;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        return InventoryResource::collection(InventoryItem::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|unique:inventory_items',
            'category' => 'required|string',
            'subcategory_id' => 'nullable|integer|exists:subcategories,id',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'cost_per_unit' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('inventory', 'public');
            $validated['image'] = $path;
        }

        $item = InventoryItem::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Item added to inventory',
            'data' => new InventoryResource($item)
        ], 201);
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'item_name' => 'required|string',
            'category' => 'required|string',
            'subcategory_id' => 'nullable|integer|exists:subcategories,id',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'cost_per_unit' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($inventory->image) {
                Storage::disk('public')->delete($inventory->image);
            }
            $path = $request->file('image')->store('inventory', 'public');
            $validated['image'] = $path;
        }

        $inventory->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Item updated successfully',
            'data' => new InventoryResource($inventory)
        ]);
    }

    public function destroy(InventoryItem $inventory)
    {
        // Delete image file if exists
        if ($inventory->image) {
            Storage::disk('public')->delete($inventory->image);
        }

        $inventory->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item deleted successfully',
            'data' => null
        ]);
    }

    public function lowStock()
    {
        $items = $this->inventoryService->getLowStockItems();
        return InventoryResource::collection($items);
    }
}
