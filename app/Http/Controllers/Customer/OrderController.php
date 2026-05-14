<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\OrderMaterial;
use App\Models\Customer;

class OrderController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return view('customer-layouts.order', ['orders' => collect()]);
        }

        $orders = OrderMaterial::with(['details.material'])
            ->where('customer_id', $customer->id)
            ->whereIn('status_order', ['paid', 'persiapan', 'dikirim', 'selesai'])
            ->latest('tanggal_order')
            ->get();

        return view('customer-layouts.order', compact('orders'));
    }
}