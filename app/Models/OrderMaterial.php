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
        return $this->belongsTo(User::class, 'customer_id');
    }
}