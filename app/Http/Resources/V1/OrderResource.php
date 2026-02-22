<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal,
            'tax' => (float) $this->tax,
            'total' => (float) $this->total,
            'advance_paid' => (float) $this->advance_paid,
            'balance_due' => (float) $this->balance_due,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_by' => $this->user->name ?? 'System',
            'created_at' => $this->created_at,
        ];
    }
}
