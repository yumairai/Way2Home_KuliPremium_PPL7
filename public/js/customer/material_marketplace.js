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
    updateFloatingCart();  // Initial load - tidak perlu debounce

    // 2. Pasang Event Listener untuk Filter & Search
    const searchBtn = document.querySelector('.search-btn');
    const searchInput = document.querySelector('.search-input');
    const applyFilterBtn = document.querySelector('.reset-filter-btn'); // Tombol "Apply Filter"
    const clearFilterBtn = document.querySelector('.clear-filter-btn');
    const sortSelect = document.querySelector('.sort-select');

    if (searchBtn) searchBtn.addEventListener('click', () => applySearchOnly());
    if (searchInput) {
        searchInput.value = urlParams.get('search') || '';
    }
    if (searchInput) {
        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                applySearchOnly();
            }
        });
        searchInput.addEventListener('input', () => debouncedApplySearchOnly());
    }
    if (applyFilterBtn) applyFilterBtn.addEventListener('click', () => applyFilters());
    if (clearFilterBtn) clearFilterBtn.addEventListener('click', () => resetFilters());
    if (sortSelect) sortSelect.addEventListener('change', () => handleSortChange());

    initPriceSlider();
});

// FUNGSI FILTER: Mengumpulkan input dan panggil fetch
async function applyFilters() {
    const price = document.getElementById('priceRange').value;
    const sort = document.querySelector('.sort-select').value;

    // Ambil kategori yang diceklis
    const categories = Array.from(document.querySelectorAll('.checkbox-input:checked'))
        .map(cb => (cb.value && cb.value.trim()) ? cb.value : (cb.nextElementSibling ? cb.nextElementSibling.innerText : ''))
        .filter(Boolean);

    // Ambil stok yang dipilih (ready | preorder)
    const stokEl = document.querySelector('.radio-input:checked');
    const stokValue = stokEl ? stokEl.value : null;

    let params = new URLSearchParams();
    params.append('harga_max', price);
    categories.forEach(c => params.append('kategori[]', c));
    if (stokValue) params.append('stok', stokValue);

    params.append('sort', sort || 'terbaru');

    const newUrl = '/material/materials?' + params.toString();
    await fetchMaterials(newUrl);
}

async function applySearchOnly() {
    const search = document.querySelector('.search-input').value.trim();
    const sort = document.querySelector('.sort-select').value;

    if (!search) {
        await fetchMaterials('/material/materials');
        return;
    }

    const params = new URLSearchParams();
    params.append('search', search);
    params.append('sort', sort || 'terbaru');

    const newUrl = '/material/materials?' + params.toString();
    await fetchMaterials(newUrl);
}

function handleSortChange() {
    const search = document.querySelector('.search-input').value.trim();
    if (search) {
        applySearchOnly();
        return;
    }

    applyFilters();
}

let searchDebounceTimer;

function debouncedApplySearchOnly() {
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(() => {
        applySearchOnly();
    }, 350);
}

async function resetFilters() {
    const searchInput = document.querySelector('.search-input');
    const sortSelect = document.querySelector('.sort-select');
    const priceSlider = document.getElementById('priceRange');
    const categoryInputs = document.querySelectorAll('.checkbox-input');
    const stockReady = document.querySelector('.radio-input[value="ready"]');

    if (searchInput) searchInput.value = '';
    if (sortSelect) sortSelect.value = '';

    categoryInputs.forEach((input) => {
        input.checked = false;
    });

    if (stockReady) {
        stockReady.checked = true;
    }

    if (priceSlider) {
        priceSlider.value = priceSlider.max;
        updatePriceSliderUI(priceSlider);
    }

    window.history.replaceState({}, '', window.location.pathname);
    await fetchMaterials('/material/materials');
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
        syncBrowserUrlWithRequest(url);

    } catch (error) {
        console.error('Error:', error);
        productGrid.innerHTML = '<p>Gagal memuat data.</p>';
    }
}

function syncBrowserUrlWithRequest(requestUrl) {
    const queryIndex = requestUrl.indexOf('?');
    const queryString = queryIndex >= 0 ? requestUrl.slice(queryIndex + 1) : '';
    const browserUrl = queryString ? `${window.location.pathname}?${queryString}` : window.location.pathname;
    window.history.replaceState({}, '', browserUrl);
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
        await W2HDialog.alert('Silakan login terlebih dahulu!');
        window.location.href = window.loginUrl;
        return;
    }

    // Optimistic: Langsung update UI sebelum network call
    const productCard = document.getElementById(`action-${id}`);
    productCard.innerHTML = `
        <div class="cart-quantity">
            <button class="cart-quantity-btn" onclick="updateCartQty(${id}, 0)">-</button>
            <input type="number" class="cart-quantity-input" value="1" 
                min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                onchange="updateCartQtyDirect(${id}, this.value)" 
                onblur="updateCartQtyDirect(${id}, this.value)">
            <button class="cart-quantity-btn" onclick="updateCartQty(${id}, 2)">+</button>
        </div>`;

    // Network call di background tanpa menunggu
    setTimeout(async () => {
        try {
            const res = await fetch('/cart/add', {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify({ material_id: id, jumlah: 1 })
            });

            if (res.ok) {
                debouncedUpdateFloatingCart();
            }
        } catch (err) {
            console.error('Error adding to cart:', err);
        }
    }, 0);
}

async function updateCartQty(id, newQty) {
    const container = document.getElementById(`action-${id}`);

    // Optimistic: Update UI dulu
    if (newQty < 1) {
        container.innerHTML = `<button class="add-to-cart-btn" onclick="handleInitialAdd(${id})">Tambah <img src="/images/icon/trolley.png"></button>`;
    } else {
        container.innerHTML = `
            <div class="cart-quantity">
                <button class="cart-quantity-btn" onclick="updateCartQty(${id}, ${newQty - 1})">-</button>
                <input type="number" class="cart-quantity-input" value="${newQty}" 
                    min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    onchange="updateCartQtyDirect(${id}, this.value)" 
                    onblur="updateCartQtyDirect(${id}, this.value)">
                <button class="cart-quantity-btn" onclick="updateCartQty(${id}, ${newQty + 1})">+</button>
            </div>`;
    }

    // Network call di background
    setTimeout(async () => {
        try {
            if (newQty < 1) {
                await fetch(`/cart/remove-material/${id}`, {
                    method: 'DELETE',
                    headers: getHeaders()
                });
            } else {
                await fetch('/cart/add', {
                    method: 'POST',
                    headers: getHeaders(),
                    body: JSON.stringify({ material_id: id, jumlah: newQty })
                });
            }
            debouncedUpdateFloatingCart();
        } catch (err) {
            console.error('Error updating cart:', err);
        }
    }, 0);
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

// Global debounce timer untuk updateFloatingCart
let floatingCartTimeout;

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

// Wrapper dengan debounce - maksimal 300ms sekali call
function debouncedUpdateFloatingCart() {
    clearTimeout(floatingCartTimeout);
    floatingCartTimeout = setTimeout(() => {
        updateFloatingCart();
    }, 300);
}

// SLIDER HARGA (Desain)
const slider = document.getElementById('priceRange');
if (slider) {
    slider.addEventListener('input', function () {
        updatePriceSliderUI(this);
    });
}

function initPriceSlider() {
    if (slider) {
        slider.value = slider.max;
        updatePriceSliderUI(slider);
    }
}

function updatePriceSliderUI(input) {
    const label = document.getElementById('currentPriceLabel');
    if (!label) return;

    const val = Number(input.value);
    const min = Number(input.min);
    const max = Number(input.max);
    const percentage = ((val - min) / (max - min)) * 100;

    input.style.background = `linear-gradient(to right, #004796 ${percentage}%, #e0e0e0 ${percentage}%)`;

    if (val >= max) {
        label.textContent = `RP 0 - ${new Intl.NumberFormat('id-ID').format(val)}++`;
    } else if (val > 0) {
        label.textContent = `RP 0 - ${new Intl.NumberFormat('id-ID').format(val)}`;
    } else {
        label.textContent = 'RP 0';
    }
}