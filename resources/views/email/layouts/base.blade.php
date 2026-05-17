<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Way2Home Notifikasi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #f0f4f8; font-family: 'Segoe UI', Arial, sans-serif; color: #2d3748; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }

        /* Header */
        .header { background: linear-gradient(135deg, #2B3361 0%, #1a2040 100%); padding: 32px 40px; text-align: center; }
        .header img { height: 36px; margin-bottom: 12px; }
        .header h1 { color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; }
        .header p { color: rgba(255,255,255,0.75); font-size: 13px; margin-top: 4px; }

        /* Body */
        .body { padding: 36px 40px; }
        .greeting { font-size: 16px; color: #4a5568; margin-bottom: 20px; }
        .greeting strong { color: #2B3361; }

        /* Status Badge */
        .status-badge { display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 20px; }
        .status-verified   { background: #d4edda; color: #155724; }
        .status-revision   { background: #fff3cd; color: #856404; }
        .status-inprogress { background: #cce5ff; color: #004085; }
        .status-done       { background: #d4edda; color: #155724; }
        .status-rejected   { background: #f8d7da; color: #721c24; }
        .status-payment    { background: #e2d9f3; color: #432874; }
        .status-default    { background: #e2e8f0; color: #4a5568; }

        /* Info Card */
        .info-card { background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; margin: 20px 0; }
        .info-card .label { font-size: 11px; color: #a0aec0; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        .info-card .value { font-size: 15px; color: #2d3748; font-weight: 600; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #edf2f7; }
        .info-row:last-child { border-bottom: none; }

        /* Progress Bar */
        .progress-wrap { margin: 20px 0; }
        .progress-label { display: flex; justify-content: space-between; font-size: 13px; color: #718096; margin-bottom: 8px; }
        .progress-track { background: #edf2f7; border-radius: 99px; height: 12px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #2B3361, #4a6cf7); transition: width 0.3s; }

        /* CTA Button */
        .cta-wrap { text-align: center; margin: 28px 0 8px; }
        .cta-btn { display: inline-block; background: linear-gradient(135deg, #2B3361, #4a6cf7); color: #ffffff !important; text-decoration: none; padding: 14px 36px; border-radius: 10px; font-size: 15px; font-weight: 600; letter-spacing: 0.3px; }

        /* Note Box */
        .note-box { background: #fffbeb; border-left: 4px solid #f6ad55; border-radius: 0 8px 8px 0; padding: 14px 18px; margin: 20px 0; font-size: 13px; color: #744210; line-height: 1.6; }

        /* Divider */
        .divider { border: none; border-top: 1px solid #edf2f7; margin: 24px 0; }

        /* Footer */
        .footer { background: #f7fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 12px; color: #a0aec0; line-height: 1.8; }
        .footer a { color: #4a6cf7; text-decoration: none; }
        .footer .social { margin: 12px 0; }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- HEADER -->
    <div class="header">
        <h1>Way2Home</h1>
        <p>Platform Jasa Pembangunan & Renovasi Rumah</p>
    </div>

    <!-- CONTENT -->
    @yield('content')

    <!-- FOOTER -->
    <div class="footer">
        <p>
            Email ini dikirim otomatis oleh sistem Way2Home.<br>
            Jangan membalas email ini. Hubungi kami melalui aplikasi jika ada pertanyaan.<br><br>
            &copy; {{ date('Y') }} Way2Home · KuliPremium PPL7<br>
        </p>
    </div>
</div>
</body>
</html>
