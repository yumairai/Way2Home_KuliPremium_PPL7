<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderMaterial;
use Illuminate\Http\Request;

class ManageOrderController extends Controller
{
    public function index()
    {
        $orders = OrderMaterial::with(['details.material', 'customer.user'])
            ->latest('tanggal_order')
            ->get();

        $statusCounts = [
            'paid'      => $orders->where('status_order', 'paid')->count(),
            'persiapan' => $orders->where('status_order', 'persiapan')->count(),
            'dikirim'   => $orders->where('status_order', 'dikirim')->count(),
            'selesai'   => $orders->where('status_order', 'selesai')->count(),
        ];

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