<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            width: 100%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        h2 {
            margin-bottom: 10px;
        }

        p {
            color: #555;
            font-size: 14px;
        }

        .alert {
            margin-top: 15px;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
        }

        .success {
            background: #e6fffa;
            color: #065f46;
        }

        .button {
            margin-top: 20px;
        }

        button {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            background-color: #4f46e5;
            color: white;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            background-color: #4338ca;
        }

        .email {
            font-weight: bold;
            color: #111;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Verifikasi Email</h2>

    <p>
        Kami telah mengirimkan link verifikasi ke:
        <br>
        <span class="email">{{ auth()->user()->email }}</span>
    </p>

    <p>
        Silakan cek inbox atau folder spam, lalu klik link untuk mengaktifkan akun kamu.
    </p>

    {{-- notif resend --}}
    @if (session('message'))
        <div class="alert success">
            {{ session('message') }}
        </div>
    @endif

    <div class="button">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit">
                Kirim Ulang Email
            </button>
        </form>
    </div>

    <p style="margin-top:20px; font-size:12px;">
        Jika tidak menerima email, tunggu beberapa detik lalu klik tombol di atas.
    </p>
</div>

</body>
</html>