document.addEventListener('DOMContentLoaded', async () => {
    const productGrid = document.getElementById('productGrid');
    const token = localStorage.getItem('token');
    const navCart = document.getElementById('navCart');

    if (token) {
        if (navCart) navCart.style.display = 'block';
    } else {
        if (navCart) navCart.style.display = 'none';
    }

    try {
        const fetchMaterials = fetch('/api/materials').then(res => res.json());
        
        const fetchCart = token 
            ? fetch('/api/cart', { headers: { 'Authorization': `Bearer ${token}` } }).then(res => res.json())
            : Promise.resolve({ data: [] });

        const [materials, cartResult] = await Promise.all([fetchMaterials, fetchCart]);
        const cartData = cartResult.data || [];

        if (!materials || materials.length === 0) {
            productGrid.innerHTML = `<div class="empty-state"><p>Data material kosong!</p></div>`;
        } else {
            renderProducts(materials, cartData);
        }

        updateFloatingCart(); 
        setupPriceSlider();

    } catch (error) {
        console.error('Error saat inisialisasi:', error);
    }
});

function renderProducts(items, cartData = []) {
    const productGrid = document.getElementById('productGrid');
    const token = localStorage.getItem('token');
    productGrid.innerHTML = ''; 

    items.forEach(item => {
        const statusBadge = item.stok > 0
            ? '<div class="product-badge badge-ready">Ready Stock</div>'
            : '<div class="product-badge badge-preorder">Pre Order</div>';

        const inCart = cartData.find(c => c.material_id === item.id);
        
        let actionHtml = '';
        if (token && inCart) {
            // Jika ada di DB, tampilkan selector jumlah
            actionHtml = `
                <div class="cart-quantity">
                    <button class="cart-quantity-btn" onclick="handleUpdateQty(${item.id}, ${inCart.jumlah - 1})">-</button>
                    <span class="cart-quantity-value">${inCart.jumlah}</span>
                    <button class="cart-quantity-btn" onclick="handleUpdateQty(${item.id}, ${inCart.jumlah + 1})">+</button>
                </div>`;
        } else {
            // Jika tidak ada, tampilkan tombol tambah biasa
            actionHtml = `
                <button class="add-to-cart-btn" onclick="handleInitialAdd(${item.id})">
                    Tambah
                    <img src="/images/icon/trolley.png" alt="Troli">
                </button>`;
        }

        const card = `
            <div class="product-card">
                <div class="product-image-container">
                    <img alt="${item.nama_material}" class="product-image" src="${item.path_foto_material}" />
                    ${statusBadge}
                </div>
                <div class="product-body">
                    <div class="product-info">
                        <h3 class="product-title">${item.nama_material}</h3>
                        <p class="product-description">${item.kategori} • ${item.deskripsi}</p>
                    </div>
                    <div class="product-footer">
                        <div class="product-price">
                            <span class="product-price-label">Rp</span>
                            <span class="product-price-value">${item.harga.toLocaleString('id-ID')}</span>
                            <span class="product-price-unit">/ ${item.satuan}</span>
                        </div>
                        <div class="product-action-container" id="action-${item.id}">
                            ${actionHtml}
                        </div>
                    </div>
                </div>
            </div>`;
        productGrid.innerHTML += card;
    });
}

// FUNGSI SLIDER HARGA
function setupPriceSlider() {
    const slider = document.getElementById('priceRange');
    const label = document.getElementById('currentPriceLabel');
    if (!slider) return;

    const updateDesign = () => {
        const val = slider.value;
        const percentage = (val - slider.min) / (slider.max - slider.min) * 100;
        slider.style.background = `linear-gradient(to right, #004796 ${percentage}%, #e0e0e0 ${percentage}%)`;
        
        if (val >= 10000000) label.textContent = "RP 10.000.000+";
        else if (val <= 0) label.textContent = "RP 0";
        else label.textContent = `RP 0 - ${new Intl.NumberFormat('id-ID').format(val)}`;
    };

    slider.addEventListener('input', updateDesign);
    updateDesign();
}

// LOGIKA TAMBAH/KURANG/HAPUS
let isProcessing = false;

async function handleInitialAdd(id) {
    if (isProcessing) return;
    const token = localStorage.getItem('token');

    if (!token) {
        alert('Silakan login terlebih dahuluuntuk menambah barang ke keranjang!');
        window.location.href = '/login';
        return;
    }

    isProcessing = true;
    const success = await updateCartAPI(id, 1);
    if (success) {
        renderQuantitySelector(id, 1);
        updateFloatingCart(); 
    }
    isProcessing = false;
}

async function handleUpdateQty(id, newQty) {
    if (isProcessing) return;
    isProcessing = true;

    const container = document.getElementById(`action-${id}`);

    if (newQty < 1) {
        const success = await deleteFromCartAPI(id);
        
        if (success) {
            container.innerHTML = `
                <button class="add-to-cart-btn" onclick="handleInitialAdd(${id})">
                    Tambah
                    <img src="/images/icon/trolley.png" alt="Troli">
                </button>`;
            
            updateFloatingCart();
        } else {
            alert("Gagal menghapus item dari keranjang.");
        }
    } else {
        const success = await updateCartAPI(id, newQty);
        if (success) {
            renderQuantitySelector(id, newQty);
            updateFloatingCart();
        }
    }
    
    isProcessing = false;
}

function renderQuantitySelector(id, qty) {
    const container = document.getElementById(`action-${id}`);
    if (container) {
        container.innerHTML = `
            <div class="cart-quantity">
                <button class="cart-quantity-btn" onclick="handleUpdateQty(${id}, ${qty - 1})">-</button>
                <span class="cart-quantity-value">${qty}</span>
                <button class="cart-quantity-btn" onclick="handleUpdateQty(${id}, ${qty + 1})">+</button>
            </div>`;
    }
}

// API CALLS
async function updateCartAPI(id, qty) {
    try {
        const response = await fetch('/api/cart/add', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ material_id: id, jumlah: qty })
        });
        return response.ok;
    } catch (err) { return false; }
}

async function deleteFromCartAPI(materialId) {
    try {
        const response = await fetch(`/api/cart/remove-material/${materialId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            const errorData = await response.json();
            console.error("Server Error:", errorData.message);
            return false;
        }

        return true;
    } catch (err) {
        console.error("Network Error (Gagal koneksi ke server):", err);
        return false;
    }
}

async function updateFloatingCart() {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
        const response = await fetch('/api/cart', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (response.ok) {
            const result = await response.json();
            const carts = result.data || [];
            
            const floatingBtn = document.querySelector('.checkout-btn');
            const countElement = document.getElementById('checkoutCount');
            const totalElement = document.getElementById('checkoutTotal');
            const navBadge = document.querySelector('.cart-badge');

            if (carts.length === 0) {
                if (floatingBtn) floatingBtn.style.display = 'none';
                if (navBadge) navBadge.innerText = '0';
                return;
            }

            let totalItems = 0;
            let totalHarga = 0;
            carts.forEach(item => {
                totalItems += item.jumlah;
                totalHarga += (item.jumlah * item.material.harga);
            });

            if (floatingBtn) {
                floatingBtn.style.display = 'flex';
                countElement.innerText = totalItems;
                totalElement.innerText = totalHarga.toLocaleString('id-ID');
            }
            if (navBadge) {
                navBadge.style.display = 'block';
                navBadge.innerText = totalItems;
            }
        }
    } catch (err) { console.error("Badge update error:", err); }
}

// Tambahkan event listener untuk tombol bayar
const checkoutBtn = document.getElementById('checkoutBtn');
if (checkoutBtn) {
    checkoutBtn.addEventListener('click', () => alert('BAYAR PAKAI MIDTRANS!'));
}