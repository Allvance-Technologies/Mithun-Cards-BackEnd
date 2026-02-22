<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::latest()->paginate(20);
        return response()->json([
            'status' => true,
            'data' => $expenses
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $expense = Expense::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Expense recorded successfully',
            'data' => $expense
        ], 201);
    }

    public function show(Expense $expense)
    {
        return response()->json([
            'status' => true,
            'data' => $expense
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $expense->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Expense updated successfully',
            'data' => $expense
        ]);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return response()->json([
            'status' => true,
            'message' => 'Expense deleted successfully',
            'data' => null
        ]);
    }
}
