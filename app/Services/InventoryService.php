<?php

namespace App\Services;

use App\Models\InventoryItem;

class InventoryService
{
    /**
     * Check if stock is sufficient for an item.
     */
    public function checkStock(string $itemName, int $requestedQuantity): bool
    {
        $item = InventoryItem::where('item_name', $itemName)->first();
        if (!$item) return false;

        return $item->stock_quantity >= $requestedQuantity;
    }

    /**
     * Reduce stock level.
     */
    public function reduceStock(string $itemName, int $quantity)
    {
        $item = InventoryItem::where('item_name', $itemName)->first();
        if ($item) {
            $item->stock_quantity -= $quantity;
            $item->save();
        }
    }

    /**
     * Get items below threshold.
     */
    public function getLowStockItems()
    {
        return InventoryItem::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->get();
    }
}
