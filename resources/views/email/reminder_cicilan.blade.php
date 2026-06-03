<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #004796; padding: 24px 32px; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; }
        .body { padding: 32px; color: #333; }
        .info-box { background: #f0f5ff; border-left: 4px solid #004796; padding: 16px 20px; border-radius: 4px; margin: 20px 0; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .info-row:last-child { margin-bottom: 0; }
        .label { color: #666; }
        .value { font-weight: bold; color: #004796; }
        .btn { display: inline-block; margin-top: 24px; padding: 12px 28px; background: #004796; color: #fff!important; text-decoration: none; border-radius: 6px; font-size: 14px; }
        .footer { padding: 16px 32px; background: #f9f9f9; font-size: 12px; color: #999; text-align: center; }
        .warning { background: #fff8e1; border-left: 4px solid #f59e0b; padding: 12px 16px; border-radius: 4px; margin-top: 16px; font-size: 13px; color: #92400e; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Way2Home — Pengingat Cicilan</h1>
    </div>
    <div class="body">
        <p>Halo, <strong>{{ $cicilan->proyek->customer->user->name }}</strong></p>
        <p>Ini adalah pengingat bahwa cicilan proyek Anda akan jatuh tempo dalam <strong>7 hari</strong>.</p>

        <div class="info-box">
            <div class="info-row">
                <span class="label">Periode </span>
                <span class="value">Cicilan {{ $cicilan->periode }}</span>
            </div>
            <div class="info-row">
                <span class="label">Nominal </span>
                <span class="value">Rp {{ number_format($cicilan->jumlah_bayar, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Jatuh Tempo </span>
                <span class="value">{{ $cicilan->tanggal_jatuh_tempo->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Alamat Proyek </span>
                <span class="value">{{ $cicilan->proyek->alamat_proyek }}</span>
            </div>
        </div>

        <div class="warning">
            ⚠️ Jika cicilan belum dibayar sesuai jadwal, pengerjaan proyek akan ditunda sementara.
        </div>

        <a href="{{ route('proyek.show', $cicilan->proyek_id) }}" class="btn">
            Bayar Sekarang
        </a>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Way2Home. Email ini dikirim otomatis, mohon tidak membalas.
    </div>
</div>
</body>
</html>