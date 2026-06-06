
const areaRange = document.getElementById("areaRange");
const areaValue = document.getElementById("areaValue");

const budgetRange = document.getElementById("budgetRange");
const budgetValue = document.getElementById("budgetValue");

function updateRangeProgress(rangeEl) {
    if (!rangeEl) return;
    const min = Number(rangeEl.min) || 0;
    const max = Number(rangeEl.max) || 100;
    const val = Number(rangeEl.value) || min;
    const progress = ((val - min) / (max - min)) * 100;
    rangeEl.style.setProperty("--range-progress", `${progress}%`);
}

// Area update
if (areaRange && areaValue) {
    areaRange.addEventListener("input", () => {
        areaValue.textContent = areaRange.value;
        updateRangeProgress(areaRange);
    });
}

// Budget format function
function formatRupiah(number) {
    return new Intl.NumberFormat("id-ID").format(number);
}

// Budget update
if (budgetRange && budgetValue) {
    budgetRange.addEventListener("input", () => {
        budgetValue.textContent = formatRupiah(budgetRange.value);
        updateRangeProgress(budgetRange);
    });
}

updateRangeProgress(areaRange);
updateRangeProgress(budgetRange);

// Priority box selector
const boxes = document.querySelectorAll(".box");
boxes.forEach(box => {
    box.addEventListener("click", () => {
        boxes.forEach(b => b.classList.remove("active"));
        box.classList.add("active");
    });
});

// Set default priority active
if (!document.querySelector('.box.active') && boxes.length > 0) {
    boxes[0].classList.add("active");
}

const submitBtn = document.getElementById("submitBtn");

submitBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    // Validasi bedrooms
    const bedrooms = parseInt(document.getElementById('bedrooms').value);
    if (!bedrooms || bedrooms < 1 || bedrooms > 6) {
        await W2HDialog.alert("Jumlah kamar tidur harus diisi antara 1 - 6.");
        document.getElementById('bedrooms').focus();
        return;
    }

    // Validasi bathrooms
    const bathrooms = parseInt(document.getElementById('bathrooms').value);
    if (!bathrooms || bathrooms < 1 || bathrooms > 3) {
        await W2HDialog.alert("Jumlah kamar mandi harus diisi antara 1 - 3.");
        document.getElementById('bathrooms').focus();
        return;
    }

    // Validasi garage
    const garage = parseInt(document.getElementById('garage').value) || 0;
    if (garage < 0 || garage > 5) {
        await W2HDialog.alert("Jumlah garasi harus antara 0 - 5.");
        document.getElementById('garage').focus();
        return;
    }

    // Validasi quality
    const quality = parseInt(document.getElementById('quality').value);
    if (!quality || quality < 1 || quality > 10) {
        await W2HDialog.alert("Kualitas desain harus diisi antara 1 - 10.");
        document.getElementById('quality').focus();
        return;
    }

    // Validasi flexibility
    const flexibility = parseFloat(document.getElementById('flexibility').value) || 0;
    if (flexibility < 0 || flexibility > 50) {
        await W2HDialog.alert("Fleksibilitas budget harus antara 0 - 50%.");
        document.getElementById('flexibility').focus();
        return;
    }

    // Validasi prioritas
    const prioritasEl = document.querySelector('.box.active');
    if (!prioritasEl) {
        await W2HDialog.alert("Silakan pilih prioritas preferensi terlebih dahulu.");
        return;
    }

    // Loading state
    submitBtn.disabled = true;
    submitBtn.textContent = "Memproses ML Engine (KNN)...";

    const payload = {
        location: document.getElementById('location').value,
        style: document.getElementById('style').value,
        area: parseInt(document.getElementById('areaRange').value),
        bedrooms: bedrooms,
        bathrooms: bathrooms,
        garage: garage,
        quality: quality,
        budget: parseInt(document.getElementById('budgetRange').value),
        //ac_required: document.getElementById('ac_required').checked,
        priority: prioritasEl.dataset.value,
        flexibility: flexibility,
    };

    try {
        const response = await fetch('/preferensi/simpan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (response.ok) {
            submitBtn.textContent = "Rekomendasi Siap! Mengalihkan...";
            setTimeout(() => {
                window.location.href = "/recommendation/result";
            }, 500);
        } else {
            await W2HDialog.error("Gagal memproses: " + (result.message || "Terjadi kesalahan."));
            submitBtn.disabled = false;
            submitBtn.textContent = "Buat Rekomendasi";
        }
    } catch (error) {
        console.error("Error:", error);
        await W2HDialog.error("Terjadi kesalahan koneksi. Pastikan server berjalan.");
        submitBtn.disabled = false;
        submitBtn.textContent = "Buat Rekomendasi";
    }
});
