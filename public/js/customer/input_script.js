
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

// biar bisa select box
const boxes = document.querySelectorAll(".box");

boxes.forEach(box => {
    box.addEventListener("click", () => {

        // hapus semua active
        boxes.forEach(b => b.classList.remove("active"));

        // set yang dipilih
        box.classList.add("active");
    });
});

const submitBtn = document.getElementById("submitBtn");

submitBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const payload = {
        lokasi: document.getElementById('lokasi').value,
        gaya_arsitektur: document.getElementById('gaya_arsitektur').value,
        luas_area: document.getElementById('areaRange').value,
        jumlah_kamar: document.getElementById('jumlah_kamar').value,
        budget: document.getElementById('budgetRange').value,
        prioritas: document.querySelector('.box.active')?.dataset.value || 'biaya'
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
            window.location.href = "/recommendation/result";
        } else {
            alert("Gagal simpan data: " + result.message);
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Terjadi kesalahan koneksi.");
    }
});