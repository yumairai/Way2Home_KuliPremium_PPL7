const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

function getHeaders(isJson = false) {
    const headers = {
        'Accept': 'application/json'
    };

    if (isJson) {
        headers['Content-Type'] = 'application/json';
    }

    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }

    return headers;
}

document.addEventListener('DOMContentLoaded', function () {
    const cartContainer = document.getElementById('cartContainer');
    const summaryContainer = document.getElementById('summaryContainer');
    const subtotalElement = document.getElementById('subtotalValue');
    const grandTotalElement = document.getElementById('grandTotalValue');

    function loadCart() {
        fetch('/cart', {
            headers: getHeaders()
        })
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    renderCart(response.data);
                    updateNavbarBadge(response.data);
                }
            })
            .catch(err => console.error("Gagal load keranjang:", err));
    }

    function renderCart(items) {
        if (!items || items.length === 0) {
            cartContainer.innerHTML = '<p class="cart-subtitle">Keranjang belanja kosong.</p>';
            summaryContainer.innerHTML = '';
            subtotalElement.innerText = 'Rp 0';
            document.getElementById('serviceFeeValue').innerText = 'Rp 0';
            grandTotalElement.innerText = 'Rp 0';
            return;
        }

        let totalHarga = 0;

        // Render Item List
        cartContainer.innerHTML = items.map(item => {
            const material = item.material;
            const harga = material.harga;
            const itemTotal = harga * item.jumlah;
            totalHarga += itemTotal;

            return `
            <div class="cart-item">
                <div class="cart-item-image-container">
                    <img class="cart-item-image" src="/${material.path_foto_material}" alt="${material.nama_material}">
                </div>
                <div class="cart-item-content">
                    <div class="cart-item-header">
                        <h3 class="cart-item-title">${material.nama_material}</h3>
                        <button class="cart-item-delete" onclick="deleteItem(${item.id})">
                            <img src="/images/icon/bin.png" alt="Delete">
                        </button>
                    </div>
                    <div class="cart-item-description">${material.deskripsi || '-'}</div>
                    <div class="cart-item-footer">
                        <span class="cart-item-price">Rp ${harga.toLocaleString('id-ID')}</span>
                        <div class="cart-quantity">
                            <button class="cart-quantity-btn" onclick="changeQty(${item.id}, ${item.jumlah - 1})">-</button>
                            <span class="cart-quantity-value">${item.jumlah}</span>
                            <button class="cart-quantity-btn" onclick="changeQty(${item.id}, ${item.jumlah + 1})">+</button>
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('');

        // --- LOGIKA BIAYA LAYANAN ---
        let serviceFee = totalHarga * 0.02; // 2%
        if (serviceFee < 5000) serviceFee = 5000;
        if (serviceFee > 50000) serviceFee = 50000;
        if (totalHarga === 0) serviceFee = 0;

        summaryContainer.innerHTML = items.map(item => `
        <div class="cart-summary-item">
            <span class="summary-item-name">${item.material.nama_material} (x${item.jumlah})</span>
            <span class="summary-item-price">Rp ${(item.jumlah * item.material.harga).toLocaleString('id-ID')}</span>
        </div>
        `).join('');

        const grandTotal = totalHarga + serviceFee;

        // Update UI
        subtotalElement.innerText = `Rp ${totalHarga.toLocaleString('id-ID')}`;
        document.getElementById('serviceFeeValue').innerText = `Rp ${serviceFee.toLocaleString('id-ID')}`;
        grandTotalElement.innerText = `Rp ${grandTotal.toLocaleString('id-ID')}`;
    }

    // --- LOGIKA CHECKOUT & MIDTRANS ---
    const checkoutBtn = document.getElementById('checkoutBtn');
    checkoutBtn.addEventListener('click', function () {
        const alamat = document.getElementById('alamat_lengkap').value;
        const nama = document.getElementById('nama_lengkap').value;
        const telepon = document.getElementById('nomor_telepon').value;

        if (!alamat || !nama || !telepon) {
            alert('Harap isi data pengiriman dengan lengkap!');
            return;
        }

        checkoutBtn.disabled = true;
        checkoutBtn.innerText = 'Memproses...';

        fetch('/payment/checkout', {
            method: 'POST',
            headers: getHeaders(true),
            body: JSON.stringify({
                alamat: alamat,
                nama: nama,
                telepon: telepon
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.snap.pay(data.token, {
                        onSuccess: function () {
                            alert("Pembayaran berhasil!");
                            window.location.href = '/material';
                        },
                        onPending: function () {
                            alert("Menunggu pembayaran... Silakan cek email atau riwayat pesanan.");
                            window.location.href = '/material';
                        },
                        onError: function () {
                            alert("Pembayaran gagal!");
                            checkoutBtn.disabled = false;
                            checkoutBtn.innerText = 'Konfirmasi & Bayar';
                        },
                        onClose: function () {
                            alert('Anda menutup jendela pembayaran sebelum selesai.');
                            checkoutBtn.disabled = false;
                            checkoutBtn.innerText = 'Konfirmasi & Bayar';
                        }
                    });
                } else {
                    alert("Gagal mendapatkan token: " + data.message);
                    checkoutBtn.disabled = false;
                    checkoutBtn.innerText = 'Konfirmasi & Bayar';
                }
            })
            .catch(err => {
                console.error(err);
                checkoutBtn.disabled = false;
                checkoutBtn.innerText = 'Konfirmasi & Bayar';
            });
    });

    // Fungsi Update Navbar
    function updateNavbarBadge(items) {
        if (window.updateNavCartBadge) {
            window.updateNavCartBadge();
            return;
        }

        const navBadge = document.querySelector('.cart-badge');
        if (navBadge) {
            let totalItems = items.reduce((acc, curr) => acc + curr.jumlah, 0);
            navBadge.innerText = totalItems;
            navBadge.style.display = totalItems > 0 ? 'inline-flex' : 'none';
        }
    }

    // Fungsi update jumlah
    window.changeQty = function (id, newQty) {
        if (newQty < 1) {
            window.deleteItem(id);
            return;
        }

        fetch(`/cart/update/${id}`, {
            method: 'PUT',
            headers: getHeaders(true),
            body: JSON.stringify({ jumlah: newQty })
        })
            .then(async res => {
                if (res.ok) {
                    loadCart();
                } else {
                    const errData = await res.json();
                    console.error("Gagal Update:", errData);
                }
            })
            .catch(err => console.error("Network Error:", err));
    };

    // Fungsi hapus item
    window.deleteItem = function (id) {
        if (!confirm('Hapus item dari keranjang?')) return;

        fetch(`/cart/delete/${id}`, {
            method: 'DELETE',
            headers: getHeaders()
        })
            .then(res => {
                if (res.ok) {
                    loadCart();
                }
            })
            .catch(err => console.error("Error saat menghapus:", err));
    };

    loadCart();
});