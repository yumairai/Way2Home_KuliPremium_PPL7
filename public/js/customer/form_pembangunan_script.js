/**
 * Way2Home - Form Pembangunan Script
 * Menggabungkan Drag & Drop, Preview, dan API Submission
 */

// 1. Inisialisasi Preview & Drag and Drop Dokumen
document.querySelectorAll('.upload-item').forEach(dropZone => {
    const input = dropZone.querySelector('.file-input-hidden');
    const previewContainer = dropZone.querySelector('.preview-container');
    const imgPreview = dropZone.querySelector('.img-preview');
    const subtitle = dropZone.querySelector('.upload-subtitle');

    // Mencegah perilaku default browser saat drag-and-drop
    ['dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, e => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    // Efek visual saat file ditarik ke atas drop zone
    dropZone.addEventListener('dragover', () => dropZone.style.borderColor = '#ffcc00');
    dropZone.addEventListener('dragleave', () => dropZone.style.borderColor = '#ccc');

    // Tangkap file yang di-drop
    dropZone.addEventListener('drop', e => {
        dropZone.style.borderColor = '#ccc';
        const files = e.dataTransfer.files;
        if (files.length) {
            input.files = files; // Sinkronkan ke input hidden
            handlePreview(files[0], previewContainer, imgPreview, subtitle);
        }
    });

    // Tangkap file yang dipilih lewat klik manual
    input.addEventListener('change', () => {
        if (input.files.length) {
            handlePreview(input.files[0], previewContainer, imgPreview, subtitle);
        }
    });
});

// Fungsi untuk menangani tampilan preview (Gambar atau Nama File PDF)
function handlePreview(file, container, img, textElement) {
    textElement.innerText = file.name;
    textElement.style.color = "#ffcc00";

    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            img.src = e.target.result;
            container.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        container.style.display = 'none';
        textElement.innerHTML = `📄 ${file.name}`;
    }
}

// 2. Logika Pemilihan Paket (Ganti Text Button & Pesan)
const packageRadios = document.querySelectorAll('input[name="package"]');
const submitBtnText = document.getElementById('submitBtnText');
const submitMsgText = document.getElementById('submitMsgText');
const packageInfo = document.getElementById('package-info');
const labelAlamat = document.getElementById('label-alamat');

if (packageRadios.length > 0 && submitBtnText) {
    packageRadios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (e.target.value === 'material-only') {
                submitBtnText.innerText = "Pesan Material Saja";
                submitMsgText.innerText = "Tim logistik kami akan mengirimkan invoice material dalam 1x24 jam.";
                packageInfo.innerHTML = "<strong>Info:</strong> Anda hanya perlu mengunggah alamat tujuan pengiriman.";
                labelAlamat.innerText = "Alamat Lengkap Pengiriman";
            } else {
                submitBtnText.innerText = "Ajukan Pembangunan";
                submitMsgText.innerText = "Tim spesialis kami akan menghubungi Anda dalam 1x24 jam setelah verifikasi.";
                packageInfo.innerHTML = "<strong>Info:</strong> Anda perlu mengunggah alamat lengkap dan dokumen pendukung.";
                labelAlamat.innerText = "Alamat Lengkap Proyek";
            }
        });
    });
}

// 3. Logika Submit Form ke API Laravel
const mainSubmitBtn = document.getElementById('mainSubmitBtn');

if (mainSubmitBtn) {
    mainSubmitBtn.addEventListener('click', async () => {
        const alamat = document.getElementById('alamatProyek');
        const selectedRadio = document.querySelector('input[name="package"]:checked');
        const selectedPackage = selectedRadio ? selectedRadio.value : '';

        let errors = [];

        // --- VALIDASI FRONTEND ---
        if (!alamat || !alamat.value.trim()) {
            errors.push("Alamat lengkap proyek belum diisi.");
        }

        // Jika pilih Paket Komplit (Jasa + Material), dokumen wajib ada
        if (selectedPackage === 'paket-komplit') {
            const cert = document.getElementById('sertifikat_tanah');
            const ktp = document.getElementById('ktp_pemilik');

            if (!cert || cert.files.length === 0) errors.push("Sertifikat Tanah wajib diunggah.");
            if (!ktp || ktp.files.length === 0) errors.push("KTP Pemilik wajib diunggah.");
        }

        if (errors.length > 0) {
            alert("Mohon lengkapi data:\n- " + errors.join("\n- "));
            return; // Berhenti di sini jika ada error
        }

        // --- PROSES KIRIM DATA (AJAX) ---

        // Ubah tampilan tombol jadi loading
        const originalText = submitBtnText.innerText;
        mainSubmitBtn.disabled = true;
        submitBtnText.innerText = "Sedang Mengirim...";

        const formData = new FormData();
        formData.append('alamat_proyek', alamat.value);
        formData.append('desain_id', 1); // Pastikan ID desain valid (biasanya diambil dari URL atau hidden input)
        formData.append('package', selectedPackage);

        // Masukkan semua file yang ada ke dalam FormData
        const fileInputs = ['sertifikat_tanah', 'ktp_pemilik', 'imb_pbg', 'surat_kuasa'];
        fileInputs.forEach(id => {
            const el = document.getElementById(id);
            if (el && el.files[0]) {
                formData.append(id, el.files[0]);
            }
        });

        try {
            const response = await fetch('/api/proyek/ajukan', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                }
            });

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                // pergi ke halaman sesuai paket
                if (selectedPackage === 'material-only') {
                    alert("Berhasil! " + data.message_2);
                    window.location.href = "/material-only";
                } else {
                    alert("Berhasil! " + data.message_1);
                    window.location.href = "/progress-track-user";
                }
            } else {
                // Jika Laravel mengembalikan error (misal file kegedean/validasi gagal)
                alert("Gagal: " + (data.message || "Terjadi kesalahan pada server"));
                resetButton(originalText);
            }
        } catch (error) {
            console.error('Error:', error);
            alert("Koneksi terputus atau server bermasalah.");
            resetButton(originalText);
        }
    });
}

function resetButton(text) {
    mainSubmitBtn.disabled = false;
    document.getElementById('submitBtnText').innerText = text;
}

const sectionDokumen = document.getElementById('sectionDokumen');
const radioPackages = document.querySelectorAll('input[name="package"]');

function toggleDokumenVisibility() {
    // Ambil value dari radio yang sedang terpilih
    const selectedValue = document.querySelector('input[name="package"]:checked').value;

    if (selectedValue === 'paket-komplit') {
        sectionDokumen.classList.add('show');
    } else {
        sectionDokumen.classList.remove('show');
    }
}

// Pasang listener untuk setiap perubahan radio
radioPackages.forEach(radio => {
    radio.addEventListener('change', toggleDokumenVisibility);
});

// Jalankan saat halaman load pertama kali
document.addEventListener('DOMContentLoaded', toggleDokumenVisibility);