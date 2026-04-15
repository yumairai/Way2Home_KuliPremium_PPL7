// memilih desain rumah yg diklik
const cards = document.querySelectorAll(".card");
cards.forEach(card => {
    card.addEventListener("click", (event) => {
        event.stopPropagation(); // Mencegah event bubbling ke document
        cards.forEach(c => c.classList.remove("active"));
        card.classList.add("active");
        console.log("Card diklik!");
    });
});

// kalo user klik di luar card, semua card hilang active
document.addEventListener("click", () => {
    cards.forEach(c => c.classList.remove("active"));
});

// fungsi tombol pilih desain
const pilihDesainBtn = document.querySelector(".container button");

pilihDesainBtn.addEventListener("click", (event) => {
    event.stopPropagation(); // mencegah event bubbling
    const activeCard = document.querySelector(".card.active");
    if (!activeCard) {
        alert("Pilih desain terlebih dahulu!");
        return;
    }
    window.location.href = "/house-build-form";
});
