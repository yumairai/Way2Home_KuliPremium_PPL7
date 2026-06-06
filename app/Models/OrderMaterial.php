<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderMaterial extends Model
{
    protected $table = 'order_material'; 
    protected $guarded = []; 

    public function details()
    {
        return $this->hasMany(DetailOrder::class, 'order_material_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status_order) {
            'pending'    => 'Menunggu Pembayaran',
            'paid'       => 'Menunggu Pengiriman',
            'persiapan'  => 'Diproses Admin',
            'dikirim'    => 'Dalam Pengiriman',
            'selesai'    => 'Pesanan Selesai',
            'expire'     => 'Kedaluwarsa',
            'cancel'     => 'Dibatalkan',
            'deny'       => 'Ditolak',
            default      => ucfirst($this->status_order),
        };
    }
    
    protected static function booted(): void
    {
        static::addGlobalScope('admin_order_isolation', function ($builder) {
            if (!app()->runningInConsole()) {
                $user = auth()->user();
                if ($user && $user->role === 'admin' && $user->is_tester) {
                    if ($user->email === 'tester.admin01@way2home.test') {
                        $builder->where('order_id_midtrans', 'like', 'W2H-TESTER-QA1-%');
                    } elseif ($user->email === 'tester.admin02@way2home.test') {
                        $builder->where('order_id_midtrans', 'like', 'W2H-TESTER-QA2-%');
                    } elseif ($user->email === 'tester.admin03@way2home.test') {
                        $builder->where('order_id_midtrans', 'like', 'W2H-TESTER-QA3-%');
                    }
                }
            }
        });
    }
}