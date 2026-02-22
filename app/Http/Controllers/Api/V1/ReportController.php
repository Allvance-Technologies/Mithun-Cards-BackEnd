<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function summary()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        
        $totalRevenue = Order::where('status', 'delivered')->sum('total');
        $monthlyRevenue = Order::where('status', 'delivered')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('total');
            
        $totalExpenses = Expense::sum('amount');
        $monthlyExpenses = Expense::where('expense_date', '>=', $startOfMonth)
            ->sum('amount');

        return response()->json([
            'status' => true,
            'message' => 'Report summary fetched',
            'data' => [
                'total_revenue' => (float) $totalRevenue,
                'monthly_revenue' => (float) $monthlyRevenue,
                'total_expenses' => (float) $totalExpenses,
                'monthly_expenses' => (float) $monthlyExpenses,
                'net_profit' => (float) ($totalRevenue - $totalExpenses),
            ]
        ]);
    }
}
