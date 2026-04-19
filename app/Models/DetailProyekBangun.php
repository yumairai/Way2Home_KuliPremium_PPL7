<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailProyekBangun extends Model
{
    use HasFactory;

    protected $table = 'detail_proyek_bangun';

    protected $fillable = [
        'proyek_id', 
        'desain_rumah_id', 
        'catatan_admin'
    ];

    public function desainRumah()
    {
        return $this->belongsTo(DesainRumah::class, 'desain_rumah_id');
    }

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    public function dokumenProyek()
    {
        return $this->hasMany(DokumenProyek::class, 'detail_bangun_id');
    }
}