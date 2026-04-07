<!DOCTYPE html>
<html lang="en">

<head>
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
                        <input type="password" id="password" placeholder="Masukkan password" required>
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

                <script>
                    const urlParams = new URLSearchParams(window.location.search);
                    const successDiv = document.getElementById('success-msg');
                    const errorDiv = document.getElementById('error-login');
                    const emailInput = document.getElementById('email');
                    const rememberCheckbox = document.getElementById('remember_me');

                    if (urlParams.has('registered')) {
                        successDiv.style.display = 'block';
                    }

                    window.onload = () => {
                        const savedEmail = localStorage.getItem('remembered_email');
                        if (savedEmail) {
                            emailInput.value = savedEmail;
                            if (rememberCheckbox) rememberCheckbox.checked = true;
                        }
                    };

                    document.querySelectorAll('input').forEach(input => {
                        input.addEventListener('input', () => {
                            successDiv.style.display = 'none';
                            errorDiv.innerText = '';
                        });
                    });

                    document.getElementById('loginForm').addEventListener('submit', async (e) => {
                        e.preventDefault();

                        errorDiv.innerText = '';

                        const email = emailInput.value;
                        const password = document.getElementById('password').value;
                        const isRemembered = rememberCheckbox ? rememberCheckbox.checked : false;

                        try {
                            const response = await fetch('/api/auth/login', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    email,
                                    password
                                })
                            });

                            const data = await response.json();

                            if (response.ok) {
                                localStorage.setItem('token', data.token);

                                if (isRemembered) {
                                    localStorage.setItem('remembered_email', email);
                                } else {
                                    localStorage.removeItem('remembered_email');
                                }

                                window.location.href = '/home';
                            } else {
                                errorDiv.innerText = data.message || 'Kredensial yang Anda masukkan salah.';
                            }
                        } catch (error) {
                            errorDiv.innerText = 'Terjadi kesalahan koneksi ke server.';
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
                <p>Memenuhi kebutuhan rumahmu</p>
            </div>
        </div>

    </div>

</body>

</html>
