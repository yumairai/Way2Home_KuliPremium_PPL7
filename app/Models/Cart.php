<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $fillable = [
        'user_id', 
        'material_id', 
        'jumlah'
    ];
    
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}