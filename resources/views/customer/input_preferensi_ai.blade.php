<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Preferensi User</title>
    <link rel="stylesheet" href="{{ asset('css/customer/input_preferensi_ai.css') }}">
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
                <a href="/">Beranda</a>
                <a href="/rekomendasi/input">Desain</a>
                <a href="/material">Material</a>
                <a href="/renovasi">Renovasi</a>
            </div>
            <!-- user bisa logout -->
            <div class="nav-actions">
                <button onclick="logout()" class="btn-nav primary">Logout</button>
            </div>
        </div>
    </nav>
    <div class="container">
        <h1>PREFERENSI</h1>
        <div class="form-card">
            <!-- ROW 1 -->
            <div class="row">
                <div class="input-group">
                    <label>PREFERENSI LOKASI</label>
                    <select id="lokasi">
                        <option>Bandung Barat</option>
                        <option>Bandung Timur</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>GAYA ARSITEKTUR</label>
                    <select id="gaya_arsitektur">
                        <option>Minimalist</option>
                        <option>Modern</option>
                        <option>Mewah</option>
                    </select>
                </div>
            </div>

            <!-- ROW 2 -->
            <div class="row">
                <div class="input-group">
                    <label>ESTIMASI AREA RUMAH (<span id="areaValue">30</span> m²)</label>

                    <input type="range" id="areaRange" min="30" max="350" value="30">

                    <div class="range-info">
                        <span>30 m²</span>
                        <span>350 m²</span>
                    </div>
                </div>

                <div class="input-group">
                    <label>JUMLAH KAMAR</label>
                    <input type="number" id="jumlah_kamar" min="1" max="10" placeholder="1 - 10">
                </div>
            </div>

            <!-- ROW 3 -->
            <div class="input-group full">
                <label>ESTIMASI BUDGET (Rp <span id="budgetValue">100000000</span>)</label>

                <input type="range" id="budgetRange" min="100000000" max="2000000000" value="100000000"
                    step="25000000">

                <div class="range-info">
                    <span>Rp 100 jt</span>
                    <span>Rp 2 M</span>
                </div>
            </div>

            <!-- PRIORITAS -->
            <div class="input-group full">
                <label>PRIORITAS PREFERENSI</label>
                <div class="priority-box">
                    <div class="box" data-value="biaya">Efisiensi Biaya</div>
                    <div class="box" data-value="estetik">Desain Estetik</div>
                    <div class="box" data-value="cepat">Konstruksi Cepat</div>
                </div>
            </div>

            <!-- BUTTON -->
            <button class="btn-submit" id="submitBtn">Buat Rekomendasi</button>
        </div>
    </div>
    <footer>
        <div class="footer-container">
            <div class="footer-brand">
                <div class="footer-brand-info">
                    <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home">
                    <span class="footer-brand-name">Way2Home</span>
                </div>
                <p class="footer-brand-text">© 2026 Way2Home Construction
                    Platform. Architectural Excellence.</p>
            </div>
            <div class="footer-links">
                <a href="#">Tentang Kami</a>
                <a href="#">Proyek</a>
                <a href="#">Karir</a>
                <a href="#">Kontak</a>
                <a href="#">Privasi</a>
            </div>
            <div class="footer-actions">
                <div class="footer-icon-btn">
                    <img src="{{ asset('images/icon/whatsapp.png') }}" alt="WhatsApp">
                </div>
            </div>
        </div>
    </footer>
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
    <script src="{{ asset('js/dropdown.js') }}"></script>
    <script src="{{ asset('js/customer/input_script.js') }}"></script>
</body>

</html>
