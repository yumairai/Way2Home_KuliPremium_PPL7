<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    use HasFactory;

    protected $table = 'proyek';

    protected $fillable = [
        'customer_id',
        'mandor_id',
        'jenis_proyek',
        'alamat_proyek',
        'tanggal_mulai',
        'status_proyek',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function mandor()
    {
        return $this->belongsTo(Mandor::class, 'mandor_id');
    }

    public function detailBangun()
    {
        return $this->hasOne(DetailProyekBangun::class, 'proyek_id');
    }

    public function pembayaranProyek()
    {
        return $this->hasMany(PembayaranProyek::class, 'proyek_id')->orderBy('periode');
    }
    
    // Shortcut: hanya DP (periode 0)
    public function pembayaranDP()
    {
        return $this->hasOne(PembayaranProyek::class, 'proyek_id')->where('periode', 0);
    }
    
    // Shortcut: hanya cicilan (periode 1-3)
    public function cicilanProyek()
    {
        return $this->hasMany(PembayaranProyek::class, 'proyek_id')->where('periode', '>', 0)->orderBy('periode');
    }
    
    // ─── Generate DP (dipanggil saat proyek pertama dibuat) ───────────
    
    public function generateDP(): void
    {
        if ($this->pembayaranProyek()->where('periode', 0)->exists()) {
            return;
        }
    
        $estimasiBiaya = $this->detailBangun->desainRumah->estimasi_biaya;
        $nominalDP     = (int) round($estimasiBiaya * 0.30);
    
        PembayaranProyek::create([
            'proyek_id'           => $this->id,
            'periode'             => 0,
            'jumlah_bayar'        => $nominalDP,
            'tanggal_jatuh_tempo' => null, // DP tidak punya jatuh tempo tetap
            'status_pembayaran'   => 'belum_bayar',
        ]);
    }
    
    // ─── Generate 3 Cicilan (dipanggil saat mandor dialokasikan) ──────
    
    public function generateCicilan(): void
    {
        if ($this->cicilanProyek()->exists()) {
            return;
        }

        $desain = $this->detailBangun->desainRumah;
        $estimasiBiaya = $desain->estimasi_biaya;
        $durasiBulan = $desain->estimasi_durasi; // Misal: 4, 8, atau 12

        // Skema Persentase Tetap
        $skema = [
            ['periode' => 1, 'persen' => 0.25, 'titik_waktu' => 0.25], // 25% dari total durasi
            ['periode' => 2, 'persen' => 0.25, 'titik_waktu' => 0.60], // 60% dari total durasi
            ['periode' => 3, 'persen' => 0.20, 'titik_waktu' => 1.00], // Akhir durasi (100%)
        ];

        $rows = [];
        foreach ($skema as $s) {
            // Menghitung bulan jatuh tempo secara dinamis
            $tambahBulan = (int) max(1, round($durasiBulan * $s['titik_waktu']));

            $rows[] = [
                'proyek_id'           => $this->id,
                'periode'             => $s['periode'],
                'jumlah_bayar'        => (int) round($estimasiBiaya * $s['persen']),
                'tanggal_jatuh_tempo' => now()->addMonths($tambahBulan)->toDateString(),
                'status_pembayaran'   => 'belum_bayar',
                'created_at'          => now(),
                'updated_at'          => now(),
            ];
        }

        PembayaranProyek::insert($rows);
    }

    public function tasks()
    {
        return $this->hasMany(ProyekMilestone::class, 'proyek_id')->orderBy('urutan');
    }

    public function aktivitas()
    {
        return $this->hasMany(ProyekAktivitas::class, 'proyek_id')->latest();
    }

    public function dokumentasi()
    {
        return $this->hasMany(ProyekDokumentasi::class, 'proyek_id')->latest();
    }

    public function progress()
    {
        return $this->hasOne(ProgressProyek::class, 'proyek_id')->latest();
    }

}