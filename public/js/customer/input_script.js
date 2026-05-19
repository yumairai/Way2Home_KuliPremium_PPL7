
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

    // Validasi jumlah kamar
    const jumlahKamar = parseInt(document.getElementById('jumlah_kamar').value);
    if (!jumlahKamar || jumlahKamar < 1 || jumlahKamar > 10) {
        await W2HDialog.alert("Jumlah kamar harus diisi antara 1 - 10.");
        document.getElementById('jumlah_kamar').focus();
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
    submitBtn.textContent = "Memproses ML Engine...";

    const payload = {
        lokasi: document.getElementById('lokasi').value,
        gaya_arsitektur: document.getElementById('gaya_arsitektur').value,
        luas_area: parseInt(document.getElementById('areaRange').value),
        jumlah_kamar: jumlahKamar,
        budget: parseInt(document.getElementById('budgetRange').value),
        prioritas: prioritasEl.dataset.value,
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
