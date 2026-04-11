// ini masih pakai data dummy yang ada di materials.json, mungkin akan beda kalo udah dari db
document.addEventListener('DOMContentLoaded', async () => {
    const productGrid = document.getElementById('productGrid');
    const response = await fetch('/data/materials.json');
    const materials = await response.json();

    // render produk ke dalam grid
    function renderProducts(items) {
        productGrid.innerHTML = ''; // bersihin grid

        items.forEach(item => {
            const statusBadge = item.stok > 0
                ? '<div class="product-badge badge-ready">Ready Stock</div>'
                : '<div class="product-badge badge-preorder">Pre Order</div>';

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
                            <button class="add-to-cart-btn">
                                Tambah
                                <img src="/images/icon/trolley.png" alt="Troli">
                            </button>
                        </div>
                    </div>
                </div>
            `;
            productGrid.innerHTML += card;
        });
    }

    // Panggil fungsi render
    if (materials.length === 0) {
        productGrid.innerHTML = `
        <div class="empty-state">
            <p>Data material kosong! Mohon coba lagi nanti.</p>
        </div>
    `;
    } else {
        renderProducts(materials);
    }
});

// rentang harga slider
document.addEventListener('DOMContentLoaded', function () {
    const slider = document.getElementById('priceRange');
    const label = document.getElementById('currentPriceLabel');

    function updateSliderDesign() {
        const val = slider.value;
        const min = slider.min ? slider.min : 0;
        const max = slider.max ? slider.max : 10000000;

        // 1. Hitung Persentase
        const percentage = (val - min) / (max - min) * 100;

        // 2. Update Warna Kuning (Linear Gradient)
        // Kita ubah background style secara langsung
        slider.style.background = `linear-gradient(to right, #004796 ${percentage}%, #e0e0e0 ${percentage}%)`;

        // 3. Update Teks Label (Format Rupiah)
        if (val >= 10000000) {
            label.textContent = "RP 10.000.000+";
        } else if (val <= 0) {
            label.textContent = "RP 0";
        }
        else {
            const formatted = new Intl.NumberFormat('id-ID').format(val);
            label.textContent = `RP 0 - ${formatted}`;
        }
    }

    // Jalankan setiap kali slider digeser
    slider.addEventListener('input', updateSliderDesign);

    // Jalankan sekali saat pertama kali halaman dibuka
    updateSliderDesign();
});

const checkoutBtn = document.getElementById('checkoutBtn');
checkoutBtn.addEventListener('click', function () {
    alert('BAYAR PAKAI MIDTRANS!');
});

