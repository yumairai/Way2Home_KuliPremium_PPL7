<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Marketplace</title>
    <link rel="stylesheet" href="{{ asset('css/material_marketplace.css') }}">
    <link href="{{ asset('images/aset/logo-w2h.png') }}" type="image" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="bg-ellipse"></div>
    <!-- NAVBAR -->
    <nav class="glass-nav">
        <div class="nav-container">
            <!-- Brand -->
            <div class="brand">
                <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home">
                <span class="brand-text">Way2Home</span>
            </div>
            <!-- link -->
            <div class="nav-links">
                <a class="active" href="#">Beranda</a>
                <a href="#">Desain</a>
                <a href="#">Material</a>
                <a href="#">Renovasi</a>
            </div>
            <!-- user bisa logout -->
            <div class="nav-actions">
                <button onclick="logout()" class="btn-nav primary">Logout</button>
            </div>
        </div>
    </nav>
    <div class="container">
        <h1>PAGE MATERIAL MARKETPLACE DAN PROGRESS TRACKING COMING SOON!</h1>
    </div>
    <script>
        // Cek apakah ada token di localStorage
        const token = localStorage.getItem('token');

        if (!token) {
            alert('Kamu belum login! Balik ke halaman login ya.');
            window.location.href = '/login';
        } else {
            document.getElementById('tokenDisplay').innerText = token;
        }

        function logout() {
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
    </script>
    <script src="{{ asset('js/material_marketplace_script.js') }}"></script>
</body>

</html>
