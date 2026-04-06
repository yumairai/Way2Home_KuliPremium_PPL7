<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailProyekBangun extends Model
{
    protected $table = 'detail_proyek_bangun';
    protected $fillable = [
        'proyek_id',
        'desain_rumah_id'
    ];
}
