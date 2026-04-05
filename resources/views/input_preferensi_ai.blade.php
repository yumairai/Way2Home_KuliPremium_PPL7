<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Preferensi User</title>
    <link rel="stylesheet" href="{{ asset('css/input_preferensi_ai.css') }}">
    <link href="{{ asset('images/aset/logo-w2h.png') }}" type="image" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="bg-ellipse"></div>
    <nav class="navbar">
        <div class="nav-left">
            <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo">
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
        <h1>PREFERENSI</h1>
        <div class="form-card">
            <!-- ROW 1 -->
            <div class="row">
                <div class="input-group">
                    <label>PREFERENSI LOKASI</label>
                    <select>
                        <option>Bandung Barat</option>
                        <option>Bandung Timur</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>GAYA ARSITEKTUR</label>
                    <select>
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
                    <input type="number" min="1" max="10" placeholder="1 - 10">
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
    <script src="{{ asset('js/recom_script.js') }}"></script>
</body>

</html>
