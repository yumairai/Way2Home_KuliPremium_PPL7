function openDocModal() {
    document.getElementById('list-proyek-modal').style.display = 'flex';
    // Reset ke item pertama setiap kali modal dibuka
    document.querySelectorAll('.proyek-item').forEach(i => i.classList.remove('active'));
    const firstItem = document.querySelector('.proyek-item');
    if (firstItem) {
        firstItem.classList.add('active');
        firstItem.click();
    }
}

function closeDocModal() {
    document.getElementById('list-proyek-modal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.proyek-item').forEach(item => {
        // Kode js buat nampilin si gambar
        item.addEventListener('click', function () {
            document.querySelectorAll('.proyek-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Tombol Submit
    document.querySelector('.modal-btn-submit').addEventListener('click', function () {
        alert('Berhasil mengassign mandor!');
        closeDocModal();
    });
});