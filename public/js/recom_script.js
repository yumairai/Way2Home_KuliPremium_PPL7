const areaRange = document.getElementById("areaRange");
const areaValue = document.getElementById("areaValue");

const budgetRange = document.getElementById("budgetRange");
const budgetValue = document.getElementById("budgetValue");

// Area update
areaRange.addEventListener("input", () => {
    areaValue.textContent = areaRange.value;
});

// Budget format function
function formatRupiah(number) {
    return new Intl.NumberFormat("id-ID").format(number);
}

// Budget update
budgetRange.addEventListener("input", () => {
    budgetValue.textContent = formatRupiah(budgetRange.value);
});

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

submitBtn.addEventListener("click", () => {
    window.location.href = "/rekomendasi/hasil";
});


