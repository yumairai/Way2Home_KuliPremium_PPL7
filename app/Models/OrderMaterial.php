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
}