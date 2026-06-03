<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\SupabaseStorageService;


class RequestRenovasi extends Model
{
    use HasFactory;

    protected $table = 'request_renovasi';

    protected $fillable = [
        'customer_id',
        'deskripsi_renovasi',
        'budget_estimasi',
        'path_foto_detail',
        'alamat',
        'status_request',
        'tanggal_request',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_request' => 'date',
        ];
    }

    public function getFotoDetailUrls(): array
    {
        $paths = $this->getPhotoPaths();
    
        if (empty($paths)) {
            return [];
        }
    
        $supabase = app(SupabaseStorageService::class);
        $urls = [];
    
        foreach ($paths as $path) {
            try {
                $urls[] = $supabase->getSignedUrl($path, 3600);
            } catch (\Throwable) {
                // Skip foto yang gagal di-sign daripada error ke seluruh halaman
            }
        }
    
        return $urls;
    }
    
    // Tambahkan helper private ini juga (jika belum ada):
    
    private function getPhotoPaths(): array
    {
        if (empty($this->path_foto_detail)) {
            return [];
        }
    
        // Kalau sudah di-cast sebagai array di $casts
        if (is_array($this->path_foto_detail)) {
            return array_filter($this->path_foto_detail);
        }
    
        // Kalau masih JSON string
        $decoded = json_decode($this->path_foto_detail, true);
        return is_array($decoded) ? array_filter($decoded) : [];
    }
 
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function penawaran()
    {
        return $this->hasMany(PenawaranRenovasi::class, 'request_renovasi_id');
    }

    public function negosiasi()
    {
        return $this->hasMany(NegosiasiRenovasi::class, 'request_renovasi_id');
    }
}
