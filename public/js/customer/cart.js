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
            .then(res => {
                if (!res.ok) {
                    console.error('Cart fetch error:', res.status, res.statusText);
                    throw new Error(`HTTP ${res.status}`);
                }
                return res.json();
            })
            .then(response => {
                console.log('Cart response:', response);
                if (response.status === 'success') {
                    renderCart(response.data);
                    updateNavbarBadge(response.data);
                } else {
                    console.warn('Cart status not success:', response);
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

            // Disable checkout button jika cart kosong
            if (checkoutBtn) {
                checkoutBtn.disabled = true;
                checkoutBtn.style.opacity = '0.5';
                checkoutBtn.style.cursor = 'not-allowed';
            }
            return;
        }

        // Enable checkout button jika ada item
        if (checkoutBtn) {
            checkoutBtn.disabled = false;
            checkoutBtn.style.opacity = '1';
            checkoutBtn.style.cursor = 'pointer';
        }

        let totalHarga = 0;

        // Render Item List
        console.log('Rendering cart items:', items.length);
        cartContainer.innerHTML = items.map(item => {
            const material = item.material;
            const harga = material.harga;
            const itemTotal = harga * item.jumlah;
            totalHarga += itemTotal;

            return `
            <div class="cart-item">
                <div class="cart-item-image-container">
                    <img class="cart-item-image" src="${material.path_foto_material}" alt="${material.nama_material}">
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
                            <input type="number" class="cart-quantity-input" value="${item.jumlah}" 
                                min="1" max="${material.stok}" oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(parseInt(this.value) > ${material.stok}) { this.value = ${material.stok}; let err = document.getElementById('cart-error-${item.id}'); if(err) { err.style.display = 'block'; setTimeout(() => err.style.display = 'none', 2000); } }"
                                onchange="changeQtyDirect(${item.id}, this.value, ${material.stok})" 
                                onblur="changeQtyDirect(${item.id}, this.value, ${material.stok})">
                            <button class="cart-quantity-btn" onclick="changeQty(${item.id}, ${item.jumlah + 1})" ${item.jumlah >= material.stok ? 'disabled' : ''}>+</button>
                        </div>
                    </div>
                    <div id="cart-error-${item.id}" style="color: #ff3b30; font-size: 11px; text-align: right; margin-top: 4px; display: none;">Melebihi stok maksimal (${material.stok})</div>
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

    if (!checkoutBtn) {
        console.error('Checkout button not found!');
    } else {
        checkoutBtn.addEventListener('click', async function () {
            const alamat = document.getElementById('alamat_lengkap').value;
            const nama = document.getElementById('nama_lengkap').value;
            const telepon = document.getElementById('nomor_telepon').value;

            if (!alamat || !nama || !telepon) {
                await W2HDialog.alert('Harap isi data pengiriman dengan lengkap!');
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
                        if (!data.token) {
                            W2HDialog.success('Pembayaran berhasil diproses.');
                            setTimeout(() => window.location.href = '/order', 1500);
                            return;
                        }
                        window.snap.pay(data.token, {
                            onSuccess: function (result) {
                                const savePrimary = document.getElementById('save_primary').checked;
                                const nama = document.getElementById('nama_lengkap').value;
                                const telepon = document.getElementById('nomor_telepon').value;
                                const alamat = document.getElementById('alamat_lengkap').value;

                                const afterPayment = () => {
                                    W2HDialog.alert("Pembayaran berhasil!");
                                    setTimeout(() => window.location.href = '/order', 1500);
                                };

                                fetch('/payment/checkout/success', {
                                    method: 'POST',
                                    headers: getHeaders(true),
                                    body: JSON.stringify({
                                        order_id: result.order_id,
                                        transaction_status: result.transaction_status,
                                    })
                                }).then(() => {
                                    if (savePrimary) {
                                        fetch('/profile/update-address-data', {
                                            method: 'POST',
                                            headers: getHeaders(true),
                                            body: JSON.stringify({ nama, telepon, alamat })
                                        }).finally(afterPayment);
                                    } else {
                                        afterPayment();
                                    }
                                });
                            },
                            onPending: function () {
                                W2HDialog.alert("Menunggu pembayaran... Silakan cek email atau riwayat pesanan.");
                                setTimeout(() => window.location.href = '/material', 1500);
                            },
                            onError: function () {
                                W2HDialog.error("Pembayaran gagal!");
                                checkoutBtn.disabled = false;
                                checkoutBtn.innerText = 'Konfirmasi & Bayar';
                            },
                            onClose: function () {
                                W2HDialog.alert('Anda menutup jendela pembayaran sebelum selesai.');
                                checkoutBtn.disabled = false;
                                checkoutBtn.innerText = 'Konfirmasi & Bayar';
                            }
                        });
                    } else {
                        W2HDialog.error(data.message || "Gagal mendapatkan token.");
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
    }

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
                    W2HDialog.error(errData.message || "Gagal memperbarui jumlah.");
                    console.error("Gagal Update:", errData);
                }
            })
            .catch(err => console.error("Network Error:", err));
    };

    window.changeQtyDirect = function(id, value, maxStock) {
        let newQty = parseInt(value);
        if (isNaN(newQty) || newQty <= 0) {
            window.changeQty(id, 0);
            return;
        }
        if (newQty > maxStock) {
            newQty = maxStock;
        }
        window.changeQty(id, newQty);
    };

    // Fungsi hapus item
    window.deleteItem = async function (id) {
        const confirmed = await W2HDialog.confirm('Hapus item dari keranjang?');
        if (!confirmed) return;

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