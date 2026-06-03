<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranProyek extends Model
{
    protected $table = 'pembayaran_proyek';

    protected $fillable = [
        'proyek_id',
        'periode',
        'jumlah_bayar',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'snap_token',
        'order_id',
        'metode_pembayaran',
        'status_pembayaran',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar'       => 'date',
    ];

    // ─── Relations ────────────────────────────────────────────────

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    // ─── Type Helpers ─────────────────────────────────────────────

    public function isDP(): bool
    {
        return $this->periode === 0;
    }

    public function isCicilan(): bool
    {
        return $this->periode > 0;
    }

    public function isBerhasil(): bool
    {
        return $this->status_pembayaran === 'berhasil';
    }

    public function isAktif(): bool
    {
        return in_array($this->status_pembayaran, ['belum_bayar', 'pending', 'gagal', 'jatuh_tempo']);
    }

    // ─── Label Helpers ────────────────────────────────────────────

    public function periodeLabel(): string
    {
        return $this->isDP() ? 'Down Payment' : 'Cicilan ' . $this->periode;
    }

    public function badgeLabel(): string
    {
        return match ($this->status_pembayaran) {
            'berhasil'    => 'Lunas',
            'pending'     => 'Menunggu Konfirmasi',
            'jatuh_tempo' => 'Jatuh Tempo',
            'gagal'       => 'Gagal',
            default       => $this->isDP() ? 'Belum Dibayar' : 'Akan Datang',
        };
    }

    public function badgeClass(): string
    {
        return match ($this->status_pembayaran) {
            'berhasil'    => 'paid',
            'pending'     => 'pending',
            'jatuh_tempo' => 'overdue',
            'gagal'       => 'overdue',
            default       => 'upcoming',
        };
    }

    public function cardClass(): string
    {
        return match ($this->status_pembayaran) {
            'berhasil' => 'completed',
            'pending'  => 'active',
            default    => 'pending',
        };
    }
}
