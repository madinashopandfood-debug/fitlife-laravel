<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $counts = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'total' => Order::count(),
            'pending' => $counts->get(Order::STATUS_PENDING, 0),
            'confirmed' => $counts->get(Order::STATUS_CONFIRMED, 0),
            'hold' => $counts->get(Order::STATUS_HOLD, 0),
            'cancelled' => $counts->get(Order::STATUS_CANCELLED, 0),
            'delivered' => $counts->get(Order::STATUS_DELIVERED, 0),
            'today' => Order::whereDate('created_at', today())->count(),
        ];

        $recentOrders = Order::latest()->limit(10)->get();

        return view('admin.dashboard.index', compact('stats', 'recentOrders'));
    }
}
