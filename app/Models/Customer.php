<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'user_id',
        'no_hp',
        'path_file_foto_ktp',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }    
    
    public function proyek()
    {
        return $this->hasMany(Proyek::class, 'customer_id');
    }

    public function requestRenovasi()
    {
        return $this->hasMany(RequestRenovasi::class, 'customer_id');
    }
}
