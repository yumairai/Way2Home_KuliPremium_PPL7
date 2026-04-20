/**
 * WAY2HOME - Material Marketplace JS
 * Gabungan: Pagination + Filter + Cart Logic (Session Based)
 */

// Global Header untuk Laravel CSRF
const getHeaders = () => ({
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
});

document.addEventListener('DOMContentLoaded', async () => {
    // 1. Ambil params dari URL (jika ada) saat pertama load
    const urlParams = new URLSearchParams(window.location.search);
    const apiUrl = '/material/materials' + (urlParams.toString() ? '?' + urlParams.toString() : '');

    await fetchMaterials(apiUrl);
    updateFloatingCart();

    // 2. Pasang Event Listener untuk Filter & Search
    const searchBtn = document.querySelector('.search-btn');
    const applyFilterBtn = document.querySelector('.reset-filter-btn'); // Tombol "Apply Filter"

    if (searchBtn) searchBtn.addEventListener('click', () => applyFilters());
    if (applyFilterBtn) applyFilterBtn.addEventListener('click', () => applyFilters());
});

// FUNGSI FILTER: Mengumpulkan input dan panggil fetch
async function applyFilters() {
    const search = document.querySelector('.search-input').value;
    const price = document.getElementById('priceRange').value;
    const sort = document.querySelector('.sort-select').value;
    
    // Ambil kategori yang diceklis
    const categories = Array.from(document.querySelectorAll('.checkbox-input:checked'))
                            .map(cb => cb.nextElementSibling.innerText);

    let params = new URLSearchParams();
    if (search) params.append('search', search);
    if (price > 0) params.append('harga_max', price);
    categories.forEach(c => params.append('kategori[]', c));
    
    // Mapping Sort
    const sortMap = { 'Harga Terendah': 'harga_rendah', 'Harga Tertinggi': 'harga_tinggi', 'Terbaru': 'terbaru' };
    params.append('sort', sortMap[sort] || 'terbaru');

    const newUrl = '/material/materials?' + params.toString();
    await fetchMaterials(newUrl);
}

// FUNGSI UTAMA: Ambil data Material & Cart
async function fetchMaterials(url) {
    const productGrid = document.getElementById('productGrid');
    const isLoggedIn = document.body.getAttribute('data-user-logged-in') === 'true';

    try {
        productGrid.innerHTML = '<div class="loader"></div>';

        // Ambil data produk & data keranjang secara paralel
        const [resMat, resCart] = await Promise.all([
            fetch(url).then(res => res.json()),
            isLoggedIn ? fetch('/cart').then(res => res.json()) : { data: [] }
        ]);

        const materials = resMat.data;
        const cartData = resCart.data || [];

        if (!materials || materials.length === 0) {
            productGrid.innerHTML = '<div class="empty-state"><p>Material tidak ditemukan.</p></div>';
            return;
        }

        // Render Produk
        productGrid.innerHTML = '';
        materials.forEach(item => {
            const cartItem = cartData.find(c => c.material_id === item.id);
            const statusBadge = item.stok > 0 
                ? '<div class="product-badge badge-ready">Ready Stock</div>' 
                : '<div class="product-badge badge-preorder">Pre Order</div>';

            let actionHtml = '';
            if (isLoggedIn && cartItem) {
                actionHtml = `
                    <div class="cart-quantity">
                        <button class="cart-quantity-btn" onclick="updateCartQty(${item.id}, ${cartItem.jumlah - 1})">-</button>
                        <input type="number" class="cart-quantity-input" value="${cartItem.jumlah}" 
                            min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            onchange="updateCartQtyDirect(${item.id}, this.value)" 
                            onblur="updateCartQtyDirect(${item.id}, this.value)">
                        <button class="cart-quantity-btn" onclick="updateCartQty(${item.id}, ${cartItem.jumlah + 1})">+</button>
                    </div>`;
            } else {
                actionHtml = `
                    <button class="add-to-cart-btn" onclick="handleInitialAdd(${item.id})">
                        Tambah <img src="/images/icon/trolley.png" alt="Troli">
                    </button>`;
            }

            productGrid.innerHTML += `
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="${item.path_foto_material}" class="product-image" alt="${item.nama_material}">
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
                                <span class="product-price-unit">/${item.satuan}</span>
                            </div>
                            <div id="action-${item.id}">${actionHtml}</div>
                        </div>
                    </div>
                </div>`;
        });

        renderPagination(resMat);

    } catch (error) {
        console.error('Error:', error);
        productGrid.innerHTML = '<p>Gagal memuat data.</p>';
    }
}

// FUNGSI PAGINATION
function renderPagination(result) {
    const container = document.getElementById('paginationContainer');
    if (!container || !result.links) return;

    let html = '';
    result.links.forEach(link => {
        const isActive = link.active ? 'active' : '';
        const isDisabled = !link.url ? 'disabled' : '';
        let label = link.label;

        // Rapikan label chevron
        if (label.includes('Previous')) label = '<img src="/images/icon/chevron-left.png">';
        else if (label.includes('Next')) label = '<img src="/images/icon/chevron-right.png">';

        html += `<button class="pagination-btn ${isActive}" ${isDisabled} onclick="fetchMaterials('${link.url}')">${label}</button>`;
    });
    container.innerHTML = html;
}

// LOGIKA KERANJANG
async function handleInitialAdd(id) {
    const isLoggedIn = document.body.getAttribute('data-user-logged-in') === 'true';
    if (!isLoggedIn) {
        alert('Silakan login terlebih dahulu!');
        window.location.href = window.loginUrl;
        return;
    }

    const res = await fetch('/cart/add', {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({ material_id: id, jumlah: 1 })
    });

    if (res.ok) {
        // Update tampilan tombol ke +/-
        document.getElementById(`action-${id}`).innerHTML = `
            <div class="cart-quantity">
                <button class="cart-quantity-btn" onclick="updateCartQty(${id}, 0)">-</button>
                <input type="number" class="cart-quantity-input" value="1" 
                    min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    onchange="updateCartQtyDirect(${id}, this.value)" 
                    onblur="updateCartQtyDirect(${id}, this.value)">
                <button class="cart-quantity-btn" onclick="updateCartQty(${id}, 2)">+</button>
            </div>`;
        updateFloatingCart();
    }
}

async function updateCartQty(id, newQty) {
    const container = document.getElementById(`action-${id}`);
    
    if (newQty < 1) {
        const res = await fetch(`/cart/remove-material/${id}`, { 
            method: 'DELETE', 
            headers: getHeaders() 
        });
        if (res.ok) {
            container.innerHTML = `<button class="add-to-cart-btn" onclick="handleInitialAdd(${id})">Tambah <img src="/images/icon/trolley.png"></button>`;
            updateFloatingCart();
        }
    } else {
        const res = await fetch('/cart/add', {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({ material_id: id, jumlah: newQty })
        });
        if (res.ok) {
            container.innerHTML = `
                <div class="cart-quantity">
                    <button class="cart-quantity-btn" onclick="updateCartQty(${id}, ${newQty - 1})">-</button>
                    <input type="number" class="cart-quantity-input" value="${newQty}" 
                        min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        onchange="updateCartQtyDirect(${id}, this.value)" 
                        onblur="updateCartQtyDirect(${id}, this.value)">
                    <button class="cart-quantity-btn" onclick="updateCartQty(${id}, ${newQty + 1})">+</button>
                </div>`;
            updateFloatingCart();
        }
    }
}

// Update quantity langsung dari input (saat user ketik dan selesai)
async function updateCartQtyDirect(id, value) {
    const newQty = parseInt(value);
    
    // Jika 0 atau kurang, hapus dari cart
    if (isNaN(newQty) || newQty <= 0) {
        await updateCartQty(id, 0); // 0 akan trigger remove
        return;
    }
    
    await updateCartQty(id, newQty);
}

async function updateFloatingCart() {
    const isLoggedIn = document.body.getAttribute('data-user-logged-in') === 'true';
    if (!isLoggedIn) return;

    try {
        const res = await fetch('/cart');
        const result = await res.json();
        const carts = result.data || [];
        
        const floatingCart = document.querySelector('.checkout-btn');
        if (!floatingCart) return;

        if (carts.length > 0) {
            const totalItems = carts.reduce((sum, i) => sum + i.jumlah, 0);
            const totalHarga = carts.reduce((sum, i) => sum + (i.jumlah * i.material.harga), 0);
            
            floatingCart.style.display = 'flex';
            document.getElementById('checkoutCount').innerText = totalItems;
            document.getElementById('checkoutTotal').innerText = totalHarga.toLocaleString('id-ID');
        } else {
            floatingCart.style.display = 'none';
        }
        
        // Update navbar cart badge
        if (window.updateNavCartBadge) window.updateNavCartBadge();
    } catch (e) { console.error(e); }
}

// SLIDER HARGA (Desain)
const slider = document.getElementById('priceRange');
if (slider) {
    slider.addEventListener('input', function() {
        const val = this.value;
        const label = document.getElementById('currentPriceLabel');
        const percentage = (val - this.min) / (this.max - this.min) * 100;
        this.style.background = `linear-gradient(to right, #004796 ${percentage}%, #e0e0e0 ${percentage}%)`;
        label.textContent = val > 0 ? `RP 0 - ${new Intl.NumberFormat('id-ID').format(val)}` : "RP 0";
    });
}