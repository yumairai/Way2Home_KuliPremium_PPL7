<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - Way2Home</title>
    <link href="{{ asset('images/logo-w2h.png') }}" type="image" rel="icon">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
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
            <div class="form-box">
                <h2>Buat Akun Baru</h2>
                <p>Silakan isi data di bawah ini untuk mendaftar</p>

                <form id="registerForm">
                    <!-- Input Username -->
                    <div class="input-group">
                        <label>Username</label>
                        <input type="text" id="name" placeholder="Pilih username" required>
                        <span id="error-name" class="error-text"></span>
                    </div>

                    <!-- Input Email -->
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" id="email" placeholder="Masukkan email aktif" required>
                        <span id="error-email" class="error-text"></span>
                    </div>

                    <!-- Input Password -->
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" id="password" placeholder="Buat password" required>
                        <span id="error-password" class="error-text"></span>
                    </div>

                    <!-- Input Konfirmasi Password -->
                    <div class="input-group">
                        <label>Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" placeholder="Ulangi password" required>
                        <span id="error-password_confirmation" class="error-text"></span>
                    </div>

                    <!-- Checkbox Syarat & Ketentuan -->
                    <div class="options">
                        <label><input type="checkbox" id="terms" required> Saya setuju dengan Syarat & Ketentuan</label>
                    </div>

                    <button type="submit" class="btn-login">Daftar Sekarang</button>

                    <p class="register">
                        Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
                    </p>
                </form>

                <script>
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
                        this.value.length < 8 ? showError('password', 'Kata sandi minimal harus terdiri dari 8 karakter.') : clearError('password');
                    });

                    document.getElementById('password_confirmation').addEventListener('input', function() {
                        const pass = document.getElementById('password').value;
                        this.value !== pass ? showError('password_confirmation', 'Konfirmasi kata sandi tidak sesuai.') : clearError('password_confirmation');
                    });


                    // --- PROSES SUBMIT FORM ---
                    document.getElementById('registerForm').addEventListener('submit', async (e) => {
                        e.preventDefault();

                        const name = document.getElementById('name').value;
                        const email = document.getElementById('email').value;
                        const password = document.getElementById('password').value;
                        const password_confirmation = document.getElementById('password_confirmation').value;

                        document.querySelectorAll('.error-text').forEach(el => el.innerText = '');

                        if (password !== password_confirmation) {
                            showError('password_confirmation', 'Konfirmasi kata sandi tidak sesuai.');
                            return;
                        }

                        try {
                            const response = await fetch('/api/auth/register', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    name,
                                    email,
                                    password,
                                    password_confirmation
                                })
                            });

                            const data = await response.json();

                            if (response.ok) {
                                window.location.href = "{{ route('login') }}?registered=true";
                            } else {
                                if (response.status === 422) {
                                    const errors = data.errors;
                                    for (const key in errors) {
                                        showError(key, errors[key][0]);
                                    }
                                } else {
                                    alert('Registrasi Gagal: ' + (data.message || 'Terjadi kesalahan sistem.'));
                                }
                            }
                        } catch (error) {
                            alert('Terjadi kesalahan koneksi ke server.');
                        }
                    });
                </script>

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

</body>

</html>