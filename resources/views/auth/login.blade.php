<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <title>Login - Way2Home</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link href="{{ asset('images/logo-w2h.png') }}" type="image" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>

<body>

    <div class="container">

        <!-- LEFT SIDE -->
        <div class="left">
            <div class="left-background">
                <img alt="Background" src="{{ asset('images/aset/construction.jpg') }}" />
            </div>
            <div class="form-box">
                <h2>Selamat Datang</h2>
                <p>Masuk ke akun Way2Home</p>

                <div id="success-msg" class="success-text" style="display: none;">
                    Registrasi berhasil! Silakan masuk dengan akun kamu.
                </div>

                <div id="error-login" class="error-text"></div>

                <form id="loginForm">
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" id="email" placeholder="Masukkan email" required>
                    </div>

                    <div class="input-group">
                        <label>Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" placeholder="Masukkan password" required>
                            <!-- Ikon Mata untuk Login -->
                            <img src="{{ asset('images/icon/tutup.png') }}" id="eye-icon-login" class="toggle-password"
                                onclick="togglePassword('password', 'eye-icon-login')" width="20">
                        </div>
                    </div>

                    <div class="options">
                        <label><input type="checkbox" id="remember_me"> Remember me</label>
                        <!-- <a href="#">Lupa Password?</a> -->
                    </div>

                    <button type="submit" class="btn-login">Masuk</button>

                    <p class="register">
                        Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
                    </p>
                </form>
            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="right">
            <div class="branding">
                <img src="{{ asset('images/logo-w2h.png') }}" alt="logo Way2Home">
                <h1>Way2Home</h1>
                <p>Memenuhi kebutuhan rumahmu</p>
            </div>
        </div>

    </div>
    <script src="{{ asset('js/auth.js') }}"></script>
    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);

            // Path gambar (sesuaikan dengan nama file kamu)
            const iconBuka = "{{ asset('images/icon/tutup.png') }}";
            const iconTutup = "{{ asset('images/icon/buka.png') }}";

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.src = iconTutup; // Ganti ke gambar mata tertutup
            } else {
                passwordInput.type = 'password';
                eyeIcon.src = iconBuka; // Balik ke gambar mata terbuka
            }
        }

        // Inisialisasi Remember Me
        const savedEmail = localStorage.getItem('remembered_email');
        if (savedEmail) {
            document.getElementById('email').value = savedEmail;
            document.getElementById('remember_me').checked = true;
        }

        document.getElementById('loginForm').addEventListener('submit', (e) => {
            e.preventDefault();
            AuthApp.clearErrors();

            AuthApp.login({
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                remember: document.getElementById('remember_me')?.checked
            });
        });
    </script>
</body>

</html>
