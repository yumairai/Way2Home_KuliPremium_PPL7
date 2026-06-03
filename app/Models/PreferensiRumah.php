<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class PreferensiRumah extends Model
{
    protected $table = 'preferensi_rumah';

    protected $fillable = [
        'customer_id',
        'lokasi',
        'gaya_arsitektur',
        'luas_area',
        'jumlah_kamar',
        'budget',
        'prioritas',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function rekomendasiRumah(): HasMany
    {
        return $this->hasMany(RekomendasiRumah::class, 'preferensi_rumah_id');
    }
}
