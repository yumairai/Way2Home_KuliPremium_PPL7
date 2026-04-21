<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class RekomendasiRumah extends Model
{
    protected $table = 'rekomendasi_rumah';

    protected $fillable = [
        'preferensi_rumah_id',
        'desain_rumah_id',
        'skor_rekomendasi',
    ];

    public function preferensiRumah(): BelongsTo
    {
        return $this->belongsTo(PreferensiRumah::class, 'preferensi_rumah_id');
    }

    public function desainRumah(): BelongsTo
    {
        return $this->belongsTo(DesainRumah::class, 'desain_rumah_id');
    }
}
