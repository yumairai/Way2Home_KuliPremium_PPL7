<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Way2Home</title>
    <style>
        body { font-family: 'Poppins', sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; }
        .card { padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; }
        button { background: #ff5a5f; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Selamat Datang di Way2Home! 🏠</h1>
        <!-- <p>Token kamu: <br><small id="tokenDisplay" style="word-break: break-all; color: gray;"></small></p> -->
        <button onclick="logout()">Keluar (Logout)</button>
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
</body>
</html>