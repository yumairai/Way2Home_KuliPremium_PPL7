/**
 * WAY2HOME - Material Marketplace JS
 * Pagination + Filter + Cart Logic (Session Based)
 */

const API_BASE_URL = '/api/materials';
const MARKETPLACE_URL = '/material';

// Global Header untuk Laravel CSRF
const getHeaders = () => ({
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
});

document.addEventListener('DOMContentLoaded', async () => {
    hydrateFilterInputsFromUrl();
    bindMarketplaceEvents();
    updateSliderVisual();

    const initialApiUrl = buildApiUrlFromCurrentLocation();
    await fetchMaterials(initialApiUrl);
    await updateFloatingCart();
});

function bindMarketplaceEvents() {
    const searchBtn = document.querySelector('.search-btn');
    const applyFilterBtn = document.querySelector('.reset-filter-btn');
    const searchInput = document.querySelector('.search-input');
    const sortSelect = document.querySelector('.sort-select');
    const slider = document.getElementById('priceRange');
    const paginationContainer = document.getElementById('paginationContainer');

    if (searchBtn) {
        searchBtn.addEventListener('click', applyFilters);
    }

    if (applyFilterBtn) {
        applyFilterBtn.addEventListener('click', applyFilters);
    }

    if (searchInput) {
        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                applyFilters();
            }
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', applyFilters);
    }

    if (slider) {
        slider.addEventListener('input', updateSliderVisual);
    }

    if (paginationContainer) {
        paginationContainer.addEventListener('click', async (event) => {
            const button = event.target.closest('.pagination-btn');
            if (!button || button.disabled) {
                return;
            }

            const pageUrl = button.dataset.url;
            if (!pageUrl) {
                return;
            }

            await fetchMaterials(pageUrl);
        });
    }
}

// FUNGSI FILTER: Mengumpulkan input dan panggil fetch
async function applyFilters() {
    const searchInput = document.querySelector('.search-input');
    const priceRange = document.getElementById('priceRange');
    const sortSelect = document.querySelector('.sort-select');
    const checkedCategoryCheckboxes = document.querySelectorAll('.checkbox-input:checked');
    const stockValue = getSelectedStockValue();

    const params = new URLSearchParams();

    const searchValue = (searchInput?.value || '').trim();
    const maxPrice = Number(priceRange?.value || 0);
    const sortValue = mapSortLabelToParam(sortSelect?.value || 'Terbaru');

    if (searchValue) {
        params.append('search', searchValue);
    }

    if (maxPrice > 0) {
        params.append('harga_max', String(maxPrice));
    }

    checkedCategoryCheckboxes.forEach((checkbox) => {
        const categoryName = checkbox.nextElementSibling?.innerText?.trim();
        if (categoryName) {
            params.append('kategori[]', categoryName);
        }
    });

    if (stockValue) {
        params.append('stok', stockValue);
    }

    params.append('sort', sortValue);

    const newUrl = `${API_BASE_URL}?${params.toString()}`;
    await fetchMaterials(newUrl);
}

function mapSortLabelToParam(sortLabel) {
    const sortMap = {
        'Harga Terendah': 'harga_rendah',
        'Harga Tertinggi': 'harga_tinggi',
        'Terbaru': 'terbaru'
    };

    return sortMap[sortLabel] || 'terbaru';
}

function mapSortParamToLabel(sortParam) {
    const labelMap = {
        harga_rendah: 'Harga Terendah',
        harga_tinggi: 'Harga Tertinggi',
        terbaru: 'Terbaru'
    };

    return labelMap[sortParam] || 'Terbaru';
}

function getSelectedStockValue() {
    const checkedStockInput = document.querySelector('.radio-input[name="stock"]:checked');
    if (!checkedStockInput) {
        return '';
    }

    const stockLabelText = checkedStockInput.nextElementSibling?.innerText?.trim().toLowerCase() || '';
    if (stockLabelText.includes('pre')) {
        return 'preorder';
    }
    if (stockLabelText.includes('stok') || stockLabelText.includes('tersedia')) {
        return 'ready';
    }

    return '';
}

function hydrateFilterInputsFromUrl() {
    const params = new URLSearchParams(window.location.search);

    const searchInput = document.querySelector('.search-input');
    const priceRange = document.getElementById('priceRange');
    const sortSelect = document.querySelector('.sort-select');
    const checkedCategories = params.getAll('kategori[]');
    const stockValue = params.get('stok');

    if (searchInput) {
        searchInput.value = params.get('search') || '';
    }

    if (priceRange) {
        priceRange.value = params.get('harga_max') || '0';
    }

    if (sortSelect) {
        const sortLabel = mapSortParamToLabel(params.get('sort'));
        const optionValues = Array.from(sortSelect.options).map((option) => option.value);
        sortSelect.value = optionValues.includes(sortLabel) ? sortLabel : 'Terbaru';
    }

    if (checkedCategories.length > 0) {
        const categoryCheckboxes = document.querySelectorAll('.checkbox-input');
        categoryCheckboxes.forEach((checkbox) => {
            const label = checkbox.nextElementSibling?.innerText?.trim();
            checkbox.checked = checkedCategories.includes(label);
        });
    }

    if (stockValue) {
        const stockInputs = document.querySelectorAll('.radio-input[name="stock"]');
        stockInputs.forEach((input) => {
            const label = input.nextElementSibling?.innerText?.trim().toLowerCase() || '';
            const isPreorder = label.includes('pre');
            const isReady = label.includes('stok') || label.includes('tersedia');

            if ((stockValue === 'preorder' && isPreorder) || (stockValue === 'ready' && isReady)) {
                input.checked = true;
            }
        });
    }
}

function buildApiUrlFromCurrentLocation() {
    const params = new URLSearchParams(window.location.search);
    return `${API_BASE_URL}${params.toString() ? `?${params.toString()}` : ''}`;
}

function syncBrowserUrlFromApiUrl(apiUrl) {
    const parsedUrl = new URL(apiUrl, window.location.origin);
    const query = parsedUrl.search || '';
    const browserUrl = `${MARKETPLACE_URL}${query}`;

    window.history.replaceState({}, '', browserUrl);
}

async function fetchJsonOrThrow(url, options) {
    const response = await fetch(url, options);
    if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
    }
    return response.json();
}

// FUNGSI UTAMA: Ambil data Material & Cart
async function fetchMaterials(url) {
    const productGrid = document.getElementById('productGrid');
    const isLoggedIn = document.body.getAttribute('data-user-logged-in') === 'true';

    if (!productGrid) {
        return;
    }

    try {
        productGrid.innerHTML = '<div class="loader"></div>';

        // Ambil data produk & data keranjang secara paralel
        const [resMat, resCart] = await Promise.all([
            fetchJsonOrThrow(url),
            isLoggedIn ? fetchJsonOrThrow('/api/cart') : Promise.resolve({ data: [] })
        ]);

        const materials = resMat.data || [];
        const cartData = resCart.data || [];

        syncBrowserUrlFromApiUrl(url);

        if (materials.length === 0) {
            productGrid.innerHTML = '<div class="empty-state"><p>Material tidak ditemukan.</p></div>';
            renderPagination(resMat);
            return;
        }

        // Render Produk
        productGrid.innerHTML = materials.map((item) => {
            const cartItem = cartData.find((c) => c.material_id === item.id);
            const statusBadge = item.stok > 0
                ? '<div class="product-badge badge-ready">Ready Stock</div>'
                : '<div class="product-badge badge-preorder">Pre Order</div>';

            let actionHtml;
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

            return `
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
        }).join('');

        renderPagination(resMat);

    } catch (error) {
        console.error('Error:', error);
        productGrid.innerHTML = '<p>Gagal memuat data.</p>';
    }
}

// FUNGSI PAGINATION
function renderPagination(result) {
    const container = document.getElementById('paginationContainer');
    if (!container || !result?.links) {
        return;
    }

    let html = '';
    result.links.forEach((link) => {
        const isActive = link.active ? 'active' : '';
        const isDisabled = !link.url;
        let label = link.label;

        // Rapikan label chevron
        if (label.includes('Previous')) {
            label = '<img src="/images/icon/chevron-left.png" alt="Sebelumnya">';
        } else if (label.includes('Next')) {
            label = '<img src="/images/icon/chevron-right.png" alt="Berikutnya">';
        }

        html += `
            <button
                class="pagination-btn ${isActive} ${isDisabled ? 'disabled' : ''}"
                ${isDisabled ? 'disabled' : ''}
                ${link.url ? `data-url="${link.url}"` : ''}
                type="button"
            >${label}</button>`;
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

    const res = await fetch('/api/cart/add', {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({ material_id: id, jumlah: 1 })
    });

    if (res.ok) {
        // Update tampilan tombol ke +/-
        const actionContainer = document.getElementById(`action-${id}`);
        if (!actionContainer) {
            return;
        }

        actionContainer.innerHTML = `
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
    if (!container) {
        return;
    }

    if (newQty < 1) {
        const res = await fetch(`/api/cart/remove-material/${id}`, {
            method: 'DELETE',
            headers: getHeaders()
        });
        if (res.ok) {
            container.innerHTML = `<button class="add-to-cart-btn" onclick="handleInitialAdd(${id})">Tambah <img src="/images/icon/trolley.png"></button>`;
            updateFloatingCart();
        }
    } else {
        const res = await fetch('/api/cart/add', {
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
    const newQty = parseInt(value, 10);

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
        const res = await fetch('/api/cart');
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
function updateSliderVisual() {
    const slider = document.getElementById('priceRange');
    const label = document.getElementById('currentPriceLabel');

    if (!slider || !label) {
        return;
    }

    const value = Number(slider.value || 0);
    const min = Number(slider.min || 0);
    const max = Number(slider.max || 1);
    const percentage = ((value - min) / (max - min)) * 100;

    slider.style.background = `linear-gradient(to right, #004796 ${percentage}%, #e0e0e0 ${percentage}%)`;
    label.textContent = value > 0 ? `RP 0 - ${new Intl.NumberFormat('id-ID').format(value)}` : 'RP 0';
}