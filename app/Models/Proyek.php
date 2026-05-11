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

        $estimasiBiaya = (int) $this->detailBangun->desainRumah->estimasi_biaya;
        $nominalDP     = (int) round($estimasiBiaya * 0.30);

        PembayaranProyek::create([
            'proyek_id'           => $this->id,
            'periode'             => 0,
            'jumlah_bayar'        => $nominalDP,
            'tanggal_jatuh_tempo' => null,
            'status_pembayaran'   => 'belum_bayar',
        ]);
    }

    // ─── Generate 3 Cicilan (dipanggil saat mandor dialokasikan) ──────

    public function generateCicilan(): void
    {
        if ($this->cicilanProyek()->exists()) {
            return;
        }

        $estimasiBiaya = (int) $this->detailBangun->desainRumah->estimasi_biaya;
        $estimasiDurasi = (int) ($this->detailBangun->desainRumah->estimasi_durasi ?? 12); // dalam bulan

        // Ambil nominal DP yang sudah tersimpan agar total tepat (bukan hitung ulang 70%)
        $nominalDP = (int) ($this->pembayaranDP?->jumlah_bayar ?? round($estimasiBiaya * 0.30));
        $sisaBiaya = $estimasiBiaya - $nominalDP; // integer, pasti tepat

        // Bagi rata ke 3 termin; sisa pembulatan masuk ke periode terakhir
        $perTermin = (int) floor($sisaBiaya / 3);
        $sisaRound = $sisaBiaya - ($perTermin * 3);

        // Bagi rata durasi proyek ke 3 interval yang sama
        $intervalBulan = (int) round($estimasiDurasi / 3);
        $tanggalMulai  = \Carbon\Carbon::parse($this->tanggal_mulai ?? now());

        $rows = [];
        for ($i = 1; $i <= 3; $i++) {
            // Periode terakhir mendapat sisa pembulatan nominal
            $nominal = $perTermin + ($i === 3 ? $sisaRound : 0);

            // Jatuh tempo: setiap interval bulan dari tanggal mulai
            // Periode 3 tepat di akhir estimasi durasi
            $jatuhTempo = ($i < 3)
                ? $tanggalMulai->copy()->addMonths($intervalBulan * $i)->toDateString()
                : $tanggalMulai->copy()->addMonths($estimasiDurasi)->toDateString();

            $rows[] = [
                'proyek_id'           => $this->id,
                'periode'             => $i,
                'jumlah_bayar'        => $nominal,
                'tanggal_jatuh_tempo' => $jatuhTempo,
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