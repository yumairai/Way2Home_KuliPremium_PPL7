function openDocModal() {
    document.getElementById('doc-modal').style.display = 'flex';
    // Reset ke item pertama setiap kali modal dibuka
    document.querySelectorAll('.doc-item').forEach(i => i.classList.remove('active'));
    const firstItem = document.querySelector('.doc-item');
    if (firstItem) {
        firstItem.classList.add('active');
        firstItem.click();
    }
}

function closeDocModal() {
    document.getElementById('doc-modal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.doc-item').forEach(item => {
        // Kode js buat nampilin si gambar
        item.addEventListener('click', function () {
            document.querySelectorAll('.doc-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            const fileUrl = this.getAttribute('data-src');
            const fileName = this.querySelector('.doc-name').textContent;
            const isPdf = fileUrl.toLowerCase().endsWith('.pdf');

            const pdfInfo = document.getElementById('pdf-info');
            const imgPreview = document.getElementById('image-preview');

            pdfInfo.style.display = 'none';
            imgPreview.style.display = 'none';

            if (isPdf) {
                pdfInfo.style.display = 'flex';
                document.getElementById('pdf-filename').textContent = fileName;
                document.getElementById('pdf-download-btn').href = fileUrl;
            } else {
                imgPreview.style.display = 'block';
                imgPreview.src = fileUrl;
            }
        });
    });

    // Kode js ini buat verif ama tolak si file, trus di kanannya ada tombol submit, artinya buat submit keseluruhan, 
    // jadi misalkan ada file yang belum diverif/tolak atau statusnya masih nunggu, maka admin belum bisa submit, 
    // karena submit itu syaratnya semua file udah diverif/tolak
    // Tombol Verifikasi
    document.querySelector('.modal-btn-approve').addEventListener('click', function () {
        const activeItem = document.querySelector('.doc-item.active');
        if (!activeItem) return;

        activeItem.classList.remove('rejected');
        activeItem.classList.add('verified');
        activeItem.querySelector('.doc-status').textContent = 'Terverifikasi';
        activeItem.querySelector('.doc-status').className = 'doc-status status-verified';

        checkSubmitEligibility();
    });

    // Tombol Tolak
    document.querySelector('.modal-btn-reject').addEventListener('click', function () {
        const activeItem = document.querySelector('.doc-item.active');
        if (!activeItem) return;

        activeItem.classList.remove('verified');
        activeItem.classList.add('rejected');
        activeItem.querySelector('.doc-status').textContent = 'Ditolak';
        activeItem.querySelector('.doc-status').className = 'doc-status status-rejected';

        checkSubmitEligibility();
    });

    // Tombol Submit
    document.querySelector('.modal-btn-submit').addEventListener('click', function () {
        const hasRejected = document.querySelectorAll('.doc-item.rejected').length > 0;
        const alasan = document.getElementById('alasan_penolakan').value.trim();

        if (hasRejected && !alasan) {
            alert('Mohon isi alasan penolakan');
            document.getElementById('alasan_penolakan').focus();
            return;
        }

        // Lanjut submit...
        alert('Berhasil disubmit!');
    });

    // Textarea — re-check saat diketik
    document.getElementById('alasan_penolakan').addEventListener('input', checkSubmitEligibility);
});


function checkSubmitEligibility() {
    const allItems = document.querySelectorAll('.doc-item');
    const submitBtn = document.querySelector('.modal-btn-submit');

    // Cek semua dokumen sudah diverifikasi atau ditolak
    const allReviewed = [...allItems].every(item =>
        item.classList.contains('verified') || item.classList.contains('rejected')
    );

    // Cek jika ada yang ditolak, alasan harus diisi
    const hasRejected = document.querySelectorAll('.doc-item.rejected').length > 0;
    const alasan = document.getElementById('alasan_penolakan').value.trim();
    const alasanOk = !hasRejected || alasan.length > 0;

    submitBtn.disabled = !(allReviewed && alasanOk);
}
