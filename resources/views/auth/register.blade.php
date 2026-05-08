<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <title>Register - Way2Home</title>
    <link href="{{ asset('images/logo-w2h.png') }}" type="image" rel="icon">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ui/dialog.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>

<body>

    <div class="container">

        <!-- LEFT SIDE -->
        <div class="left">
            <div class="left-background">
                <img alt="Background" src="{{ asset('images/aset/construction2.jpg') }}" />
            </div>
            <div class="form-box">
                <h2>Buat Akun Baru</h2>
                <p>Silakan isi data di bawah ini untuk mendaftar</p>

                <form id="registerForm">
                    <!-- Input Nama Lengkap -->
                    <div class="input-group">
                        <label>Nama Lengkap</label>
                        <input type="text" id="name" placeholder="Masukkan nama lengkap" required>
                        <span id="error-name" class="error-text"></span>
                    </div>

                    <!-- Input Email -->
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" id="email" placeholder="Masukkan email aktif" required>
                        <span id="error-email" class="error-text"></span>
                    </div>

                    <!-- Input Nomor HP -->
                    <div class="input-group">
                        <label>Nomor HP</label>
                        <input type="tel" id="phone_number" placeholder="Contoh: 081234567xxx" required
                            pattern="[0-9]{10,14}" title="Masukkan 10-14 digit angka">
                        <span id="error-phone_number" class="error-text"></span>
                    </div>

                    <!-- Input Password -->
                    <div class="input-group">
                        <label>Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" placeholder="Buat password" required>
                            <!-- Ikon Mata -->
                            <img src="{{ asset('images/icon/tutup.png') }}" id="eye-icon-password"
                                class="toggle-password" onclick="togglePassword('password', 'eye-icon-password')"
                                width="20">
                        </div>
                        <span id="error-password" class="error-text"></span>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="input-group">
                        <label>Konfirmasi Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="password_confirmation" placeholder="Ulangi password" required>
                            <img src="{{ asset('images/icon/tutup.png') }}" id="eye-icon-confirm"
                                class="toggle-password"
                                onclick="togglePassword('password_confirmation', 'eye-icon-confirm')" width="20">
                        </div>
                        <span id="error-password_confirmation" class="error-text"></span>
                    </div>

                    <!-- Checkbox Syarat & Ketentuan -->
                    <div class="options">
                        <label><input type="checkbox" id="terms" required> Saya setuju dengan Syarat &
                            Ketentuan</label>
                    </div>

                    <button type="submit" class="btn-login">Daftar Sekarang</button>

                    <p class="register">
                        Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
                    </p>
                </form>
            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="right">
            <div class="branding">
                <img src="{{ asset('images/logo-w2h.png') }}" alt="logo Way2Home">
                <h1>Way2Home</h1>
                <p>Membuat rumahmu lebih berwarna</p>
            </div>
        </div>

    </div>
    @include('partials.w2h-dialog')
    @include('partials.w2h-flash')
    <script src="{{ asset('js/ui/dialog.js') }}"></script>
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

        const showError = (id, msg) => {
            const el = document.getElementById(`error-${id}`);
            if (el) el.innerText = msg;
        };

        const clearError = (id) => {
            const el = document.getElementById(`error-${id}`);
            if (el) el.innerText = '';
        };

        document.getElementById('email').addEventListener('input', function() {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            !regex.test(this.value) ? showError('email', 'Format alamat email tidak valid.') : clearError('email');
        });

        document.getElementById('password').addEventListener('input', function() {
            this.value.length < 8 ? showError('password', 'Kata sandi minimal harus terdiri dari 8 karakter.') :
                clearError('password');
        });

        document.getElementById('password_confirmation').addEventListener('input', function() {
            const pass = document.getElementById('password').value;
            this.value !== pass ? showError('password_confirmation', 'Konfirmasi kata sandi tidak sesuai.') :
                clearError('password_confirmation');
        });

        document.getElementById('phone_number').addEventListener('input', function() {
            const regex = /^[0-9]{10,14}$/;
            !regex.test(this.value) ? showError('phone_number', 'Format nomor HP tidak valid.') : clearError(
                'phone_number');
        });

        document.getElementById('registerForm').addEventListener('submit', (e) => {
            e.preventDefault();
            AuthApp.clearErrors();

            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone_number: document.getElementById('phone_number').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value
            };

            AuthApp.register(formData);
        });
    </script>
</body>

</html>
