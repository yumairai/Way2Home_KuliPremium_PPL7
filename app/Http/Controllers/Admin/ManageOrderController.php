<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderMaterial;
use Illuminate\Http\Request;

class ManageOrderController extends Controller
{
    public function index()
    {
        $ordersQuery = OrderMaterial::with(['details.material', 'customer.user'])
            ->latest('tanggal_order');

        $allOrders = $ordersQuery->get();

        $statusCounts = [
            'paid'      => $allOrders->where('status_order', 'paid')->count(),
            'persiapan' => $allOrders->where('status_order', 'persiapan')->count(),
            'dikirim'   => $allOrders->where('status_order', 'dikirim')->count(),
            'selesai'   => $allOrders->where('status_order', 'selesai')->count(),
        ];

        $orders = $ordersQuery->paginate(6);

        return view('admin.manajemen_order', compact('orders', 'statusCounts'));
    }

    public function updateStatus(Request $request, OrderMaterial $order)
    {
        $request->validate([
            'status' => 'required|in:persiapan,dikirim,selesai',
        ]);

        $order->update(['status_order' => $request->status]);

        return response()->json([
            'success' => true,
            'status'  => $order->status_order,
        ]);
    }
}