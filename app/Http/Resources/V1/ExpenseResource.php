<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'amount' => (float) $this->amount,
            'expense_date' => $this->expense_date,
            'notes' => $this->notes,
        ];
    }
}
