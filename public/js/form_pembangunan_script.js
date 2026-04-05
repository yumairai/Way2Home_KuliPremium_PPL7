document.querySelectorAll('.file-input-hidden').forEach(input => {
    input.addEventListener('change', function () {
        const fileName = this.files[0] ? this.files[0].name : "Pilih file atau drag & drop";
        const subtitle = this.parentElement.querySelector('.upload-subtitle');
        subtitle.innerText = fileName;
        subtitle.style.color = "#ffcc00";
    });
});

document.querySelectorAll('.upload-item').forEach(dropZone => {
    const input = dropZone.querySelector('.file-input-hidden');
    const previewContainer = dropZone.querySelector('.preview-container');
    const imgPreview = dropZone.querySelector('.img-preview');
    const subtitle = dropZone.querySelector('.upload-subtitle');

    // efek saat drag file
    ['dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, e => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    dropZone.addEventListener('dragover', () => dropZone.style.borderColor = '#ffcc00');
    dropZone.addEventListener('dragleave', () => dropZone.style.borderColor = '#ccc');

    // tangkap file yang di drop
    dropZone.addEventListener('drop', e => {
        dropZone.style.borderColor = '#ccc';
        const files = e.dataTransfer.files;
        if (files.length) {
            input.files = files; // Masukkan file ke input hidden
            handlePreview(files[0], previewContainer, imgPreview, subtitle);
        }
    });

    // tangkap file yang dipilih lewat klik
    input.addEventListener('change', () => {
        if (input.files.length) {
            handlePreview(input.files[0], previewContainer, imgPreview, subtitle);
        }
    });
});

// preview file 
function handlePreview(file, container, img, textElement) {
    textElement.innerText = file.name;

    // jika file gambar, tampilkan preview gambarnya
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            img.src = e.target.result;
            container.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        // untuk pdf agar bisa muncul nama filenya
        container.style.display = 'none';
        textElement.innerHTML = `📄 ${file.name}`;
    }
}

const packageRadios = document.querySelectorAll('input[name="package"]');
const submitBtnText = document.getElementById('submitBtnText');
const submitMsgText = document.getElementById('submitMsgText');

if (packageRadios.length > 0 && submitBtnText) {
    packageRadios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (e.target.value === 'material-only') {
                submitBtnText.innerText = "Pesan Material";
                submitMsgText.innerText = "Tim logistik kami akan mengirimkan invoice material dalam 1x24 jam.";
            } else {
                submitBtnText.innerText = "Ajukan Pembangunan";
                submitMsgText.innerText = "Tim spesialis kami akan menghubungi Anda dalam 1x24 jam setelah verifikasi.";
            }
        });
    });
}

const mainSubmitBtn = document.getElementById('mainSubmitBtn');

if (mainSubmitBtn) {
    mainSubmitBtn.addEventListener('click', () => {
        const alamat = document.getElementById('alamatProyek');
        const selectedRadio = document.querySelector('input[name="package"]:checked');
        const selectedPackage = selectedRadio ? selectedRadio.value : '';

        let errors = [];

        // validasi alamat proyek
        if (!alamat || !alamat.value.trim()) {
            errors.push("Alamat lengkap proyek belum diisi.");
        }

        // validasi dokumen untuk paket lengkap
        if (selectedPackage === 'paket-komplit') {
            const sertifikat = document.getElementById('sertifikat_tanah');
            const ktp = document.getElementById('ktp_pemilik');
            const imb = document.getElementById('imb_pbg');

            if (!sertifikat || sertifikat.files.length === 0) errors.push("Sertifikat Tanah wajib ada.");
            if (!ktp || ktp.files.length === 0) errors.push("KTP Pemilik wajib ada.");
            if (!imb || imb.files.length === 0) errors.push("IMB/PBG wajib ada.");
        }

        // otw ke halaman sesuai pilihan paket jika tidak ada error
        if (errors.length > 0) {
            alert("Mohon lengkapi data:\n- " + errors.join("\n- "));
        } else {
            if (selectedPackage === 'material-only') {
                window.location.href = "/material-only";
            } else {
                window.location.href = "/progress-track-user";
            }
        }
    });
}