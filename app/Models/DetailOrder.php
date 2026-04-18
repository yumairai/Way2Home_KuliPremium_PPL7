<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailOrder extends Model
{
    protected $table = 'detail_order';
    protected $guarded = [];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function order()
    {
        return $this->belongsTo(OrderMaterial::class, 'order_material_id');
    }
}