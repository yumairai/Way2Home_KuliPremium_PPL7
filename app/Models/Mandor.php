<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mandor extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope('admin_specific_mandor', function ($builder) {
            if (!app()->runningInConsole()) {
                $user = auth()->user();
                if ($user && $user->role === 'admin' && $user->is_tester) {
                    if ($user->email === 'tester.admin01@way2home.test') {
                        $builder->where('mandors.user_id', 5);
                    } elseif ($user->email === 'tester.admin02@way2home.test') {
                        $builder->where('mandors.user_id', 6);
                    } elseif ($user->email === 'tester.admin03@way2home.test') {
                        $builder->where('mandors.user_id', 7);
                    }
                }
            }
        });
    }
    protected $table = 'mandors';

    protected $fillable = [
        'user_id',
        'sertifikasi',
        'path_foto_profil',
        'area_kerja',
        'path_foto_ktp',
        'lama_pengalaman',
        'status',
        'rating',
        'is_ghost',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Proyek yang sedang aktif (mandor_id mengarah ke id mandor ini)
    public function proyekAktif()
    {
        return $this->hasOne(Proyek::class, 'mandor_id')
                    ->whereNotIn('status_proyek', ['Selesai', 'Dibatalkan']);
    }

    // Cek apakah mandor sedang mengerjakan renovasi aktif
    public function renovasiAktif()
    {
        return $this->hasOne(PenawaranRenovasi::class, 'mandor_id')
                    ->where('status_penawaran', 'diterima')
                    ->whereHas('requestRenovasi', function ($query) {
                        $query->where('status_request', '!=', 'selesai');
                    });
    }

    public function penawaranRenovasi()
    {
        return $this->hasMany(PenawaranRenovasi::class, 'mandor_id');
    }
}
