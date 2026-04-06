<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreferensiRumah extends Model
{
protected $table = 'preferensi_rumah';
protected $fillable = 
[
    'customer_id', 
    'lokasi', 
    'gaya_arsitektur', 
    'luas_area', 
    'jumlah_kamar', 
    'budget', 
    'prioritas'
];

public function customer()
{
    return $this->belongsTo(Customer::class);
}
}