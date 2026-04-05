<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekomendasi Rumah</title>
    <link rel="stylesheet" href="{{ asset('css/rekomendasi_rumah.css') }}">
    <link href="{{ asset('images/aset/logo-w2h.png') }}" type="image" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <div class="nav-left">
            <img src="{{ asset('images//aset/logo-w2h.png') }}" alt="Logo">
            <span class="brand">Way2Home</span>
        </div>

        <div class="nav-right">
            <a href="#">Beranda</a>
            <a href="#">Desain</a>
            <a href="#">Material</a>
            <a href="#">Renovasi</a>

            <div class="profile">
                <img src="{{ asset('images/aset/user-dummy.jpg') }}" alt="profile">
            </div>
        </div>
    </nav>
    <div class="container">
        <h1>REKOMENDASI RUMAH</h1>
        <h3>Ai Generated</h3>
        <div class="card-container">
            <div class="card">
                <p>Desain 1</p>
                <img src="{{ asset('images/rekomendasi/rekom1.jpg') }}" alt="Rumah 1">
                <div class="details">
                    <h2>Modern Minimalist</h2>
                    <p>Estimasi Biaya: Rp 400.000.000</p>
                    <p>Area: 50 m²</p>
                    <p>Estimasi Waktu: 6 Bulan</p>
                </div>
            </div>

            <div class="card">
                <p>Desain 2</p>
                <img src="{{ asset('images/rekomendasi/rekom2.jpg') }}" alt="Rumah 2">
                <div class="details">
                    <h2>Modern Minimalist</h2>
                    <p>Estimasi Biaya: Rp 500.000.000</p>
                    <p>Area: 80 m²</p>
                    <p>Estimasi Waktu: 8 Bulan</p>
                </div>
            </div>

            <div class="card">
                <p>Desain 3</p>
                <img src="{{ asset('images/rekomendasi/rekom3.jpg') }}" alt="Rumah 3">
                <div class="details">
                    <h2>Modern Minimalist</h2>
                    <p>Estimasi Biaya: Rp 600.000.000</p>
                    <p>Area: 150 m²</p>
                    <p>Estimasi Waktu: 12 Bulan</p>
                </div>
            </div>
        </div>
        <button>Pilih Desain</button>
    </div>
    <script src="{{ asset('js/recom_script.js') }}"></script>
</body>

</html>
