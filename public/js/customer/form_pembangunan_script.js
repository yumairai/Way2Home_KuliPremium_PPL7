/**
 * Way2Home - Form Pembangunan Script
 * Menggabungkan Drag & Drop, Preview, dan API Submission
 */

const MAX_FILE_SIZE = 2 * 1024 * 1024;
const documentFields = [
    { id: 'sertifikat_tanah', label: 'Sertifikat Tanah', required: true },
    { id: 'ktp_pemilik', label: 'KTP Pemilik', required: true },
    { id: 'imb_pbg', label: 'IMB/PBG', required: true },
    { id: 'surat_kuasa', label: 'Surat Kuasa', required: false },
];

const packageRadios = document.querySelectorAll('input[name="package"]');
const submitBtnText = document.getElementById('submitBtnText');
const submitMsgText = document.getElementById('submitMsgText');
const packageInfo = document.getElementById('package-info');
const labelAlamat = document.getElementById('label-alamat');
const mainSubmitBtn = document.getElementById('mainSubmitBtn');
const sectionDokumen = document.getElementById('sectionDokumen');
const sectionAlamat = document.getElementById('sectionAlamat');
const alamatInput = document.getElementById('alamatProyek');
const alamatError = document.getElementById('alamat-error');
const desainInput = document.getElementById('desain_id');
const infoBox = document.querySelector('.info-box');
let submitAttempted = false;

function getSelectedPackage() {
    const selectedRadio = document.querySelector('input[name="package"]:checked');
    return selectedRadio ? selectedRadio.value : '';
}

function getDocumentContext(input) {
    const dropZone = input.closest('.upload-item');
    return {
        dropZone,
        previewContainer: dropZone ? dropZone.querySelector('.preview-container') : null,
        imgPreview: dropZone ? dropZone.querySelector('.img-preview') : null,
        subtitle: dropZone ? dropZone.querySelector('.upload-subtitle') : null,
        errorElement: dropZone ? dropZone.querySelector('.field-error') : null,
    };
}

function rememberSubtitle(subtitle) {
    if (subtitle && !subtitle.dataset.defaultText) {
        subtitle.dataset.defaultText = subtitle.textContent.trim();
    }
}

function resetDocumentPreview(context) {
    if (context.previewContainer) {
        context.previewContainer.style.display = 'none';
    }

    if (context.imgPreview) {
        context.imgPreview.src = '';
    }
}

function setDocumentError(input, message) {
    const context = getDocumentContext(input);

    if (context.dropZone) {
        context.dropZone.classList.toggle('has-error', Boolean(message));
        context.dropZone.setAttribute('aria-invalid', message ? 'true' : 'false');
    }

    if (context.errorElement) {
        context.errorElement.textContent = message || '';
    }
}

function setDocumentState(input, { isValid = false, isDragging = false } = {}) {
    const context = getDocumentContext(input);

    if (!context.dropZone) {
        return;
    }

    context.dropZone.classList.toggle('is-valid', isValid);
    context.dropZone.classList.toggle('is-dragging', isDragging);
}

function showDocumentPreview(file, context) {
    if (!context.subtitle) {
        return;
    }

    rememberSubtitle(context.subtitle);
    context.subtitle.style.color = '#ffcc00';
    context.subtitle.textContent = file.name;

    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = event => {
            if (context.imgPreview) {
                context.imgPreview.src = event.target.result;
            }

            if (context.previewContainer) {
                context.previewContainer.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    } else {
        resetDocumentPreview(context);
    }
}

function clearDocumentSubtitle(context) {
    if (!context.subtitle) {
        return;
    }

    rememberSubtitle(context.subtitle);
    context.subtitle.style.color = '';
    context.subtitle.textContent = context.subtitle.dataset.defaultText || 'Pilih file atau drag & drop';
}

function syncDocumentField(input, { required, label }, showMissingError, renderPreview = false) {
    const context = getDocumentContext(input);
    const selectedFile = input.files && input.files[0] ? input.files[0] : null;
    const isPackageKomplit = getSelectedPackage() === 'paket-komplit';
    const isRequired = required && isPackageKomplit;

    if (!selectedFile) {
        resetDocumentPreview(context);
        clearDocumentSubtitle(context);
        setDocumentState(input, { isValid: false, isDragging: false });

        if (isRequired && showMissingError) {
            setDocumentError(input, `${label} wajib diisi.`);
        } else {
            setDocumentError(input, '');
        }

        return !isRequired;
    }

    if (selectedFile.size > MAX_FILE_SIZE) {
        input.value = '';
        resetDocumentPreview(context);
        clearDocumentSubtitle(context);
        setDocumentState(input, { isValid: false, isDragging: false });
        setDocumentError(input, `${label} lebih dari 2 MB.`);
        return false;
    }

    setDocumentError(input, '');
    setDocumentState(input, { isValid: true, isDragging: false });

    if (renderPreview) {
        showDocumentPreview(selectedFile, context);
    }

    return true;
}

function setAddressError(message) {
    const addressGroup = alamatInput ? alamatInput.closest('.form-group') : null;

    if (addressGroup) {
        addressGroup.classList.toggle('has-error', Boolean(message));
    }

    if (alamatInput) {
        alamatInput.classList.toggle('has-error', Boolean(message));
        alamatInput.setAttribute('aria-invalid', message ? 'true' : 'false');
    }

    if (alamatError) {
        alamatError.textContent = message || '';
    }
}

function validateAddress(showMissingError) {
    if (getSelectedPackage() === 'material-only') {
        setAddressError('');
        return true;
    }

    if (!alamatInput || !alamatInput.value.trim()) {
        setAddressError(showMissingError ? 'Alamat lengkap proyek wajib diisi.' : '');
        return false;
    }

    setAddressError('');
    return true;
}

function hasAnyUserInput() {
    if (getSelectedPackage() === 'material-only') {
        return true;
    }

    const addressFilled = Boolean(alamatInput && alamatInput.value.trim());
    const documentFilled = documentFields.some(field => {
        const input = document.getElementById(field.id);
        return Boolean(input && input.files && input.files.length > 0);
    });

    return addressFilled || documentFilled;
}

function syncSubmitButtonState() {
    if (mainSubmitBtn) {
        mainSubmitBtn.disabled = !hasAnyUserInput();
    }
}

function validateFormState(showMissingErrors = false) {
    const selectedPackage = getSelectedPackage();
    const addressValid = validateAddress(showMissingErrors);
    let documentsValid = true;

    documentFields.forEach(field => {
        const input = document.getElementById(field.id);
        if (!input) {
            return;
        }

        if (selectedPackage === 'paket-komplit') {
            const fieldValid = syncDocumentField(input, field, showMissingErrors, false);
            documentsValid = documentsValid && fieldValid;
        } else {
            setDocumentError(input, '');
        }
    });

    const isValid = addressValid && documentsValid && Boolean(desainInput && desainInput.value);

    return isValid;
}

function handleDocumentChange(input, field) {
    syncDocumentField(input, field, submitAttempted, true);

    if (submitAttempted) {
        validateFormState(true);
    } else {
        syncSubmitButtonState();
    }
}

function handleDocumentDrop(input, field, file) {
    if (!file) {
        return;
    }

    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    input.files = dataTransfer.files;
    handleDocumentChange(input, field);
}

function updatePackageUi() {
    const selectedPackage = getSelectedPackage();

    if (selectedPackage === 'material-only') {
        if (submitBtnText) {
            submitBtnText.innerText = 'Pesan Material Saja';
        }
        if (submitMsgText) {
            submitMsgText.innerText = 'Tim logistik kami akan mengirimkan invoice material dalam 1x24 jam.';
        }
        if (packageInfo) {
            packageInfo.innerHTML = '<strong>Info:</strong> Anda akan diarahkan ke keranjang untuk proses pemesanan.';
        }
        if (sectionAlamat) {
            sectionAlamat.style.display = 'none';
        }
        if (infoBox) {
            infoBox.style.display = 'none';
        }
    } else {
        if (submitBtnText) {
            submitBtnText.innerText = 'Ajukan Pembangunan';
        }
        if (submitMsgText) {
            submitMsgText.innerText = 'Tim spesialis kami akan menghubungi Anda dalam 1x24 jam setelah verifikasi dokumen.';
        }
        if (packageInfo) {
            packageInfo.innerHTML = '<strong>Info:</strong> Anda perlu mengunggah alamat lengkap dan dokumen pendukung.';
        }
        if (sectionAlamat) {
            sectionAlamat.style.display = 'block';
        }
        if (infoBox) {
            infoBox.style.display = 'block';
        }
        if (labelAlamat) {
            labelAlamat.innerText = 'Alamat Lengkap Proyek';
        }
    }

    if (sectionDokumen) {
        if (selectedPackage === 'paket-komplit') {
            sectionDokumen.classList.add('show');
            sectionDokumen.style.display = 'block';
        } else {
            sectionDokumen.classList.remove('show');
            sectionDokumen.style.display = 'none';
        }
    }

    if (submitAttempted) {
        validateFormState(true);
    } else {
        syncSubmitButtonState();
    }
}

function initializeDocumentValidation() {
    document.querySelectorAll('.upload-item').forEach(dropZone => {
        const input = dropZone.querySelector('.file-input-hidden');
        const subtitle = dropZone.querySelector('.upload-subtitle');

        if (!input) {
            return;
        }

        rememberSubtitle(subtitle);

        ['dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, event => {
                event.preventDefault();
                event.stopPropagation();
            });
        });

        dropZone.addEventListener('dragover', () => {
            dropZone.classList.add('is-dragging');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('is-dragging');
        });

        dropZone.addEventListener('drop', event => {
            dropZone.classList.remove('is-dragging');
            handleDocumentDrop(input, documentFields.find(field => field.id === input.id), event.dataTransfer.files[0]);
        });

        input.addEventListener('change', () => {
            handleDocumentChange(input, documentFields.find(field => field.id === input.id));
        });
    });
}

// 3. Logika Submit Form ke API Laravel
if (mainSubmitBtn) {
    mainSubmitBtn.addEventListener('click', async () => {
        submitAttempted = true;

        if (!validateFormState(true)) {
            syncSubmitButtonState();
            return; // Berhenti di sini jika ada error
        }

        const alamat = alamatInput;
        const selectedPackage = getSelectedPackage();

        // --- PROSES KIRIM DATA (AJAX) ---

        // Ubah tampilan tombol jadi loading
        const originalText = submitBtnText.innerText;
        mainSubmitBtn.disabled = true;
        submitBtnText.innerText = "Sedang Mengirim...";

        const formData = new FormData();
        formData.append('alamat_proyek', alamat.value);
        formData.append('desain_id', desainInput.value);
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
            const response = await fetch('/proyek/ajukan', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                // pergi ke halaman sesuai paket
                if (selectedPackage === 'material-only') {
                    await W2HDialog.success("Berhasil! " + data.message_2);
                    setTimeout(() => window.location.href = "/material/cart", 1500);
                } else {
                    await W2HDialog.success("Berhasil! " + data.message_1);
                    setTimeout(() => window.location.href = "/proyek", 1500);
                }
            } else {
                // Jika Laravel mengembalikan error (misal file kegedean/validasi gagal)
                await W2HDialog.error("Gagal: " + (data.message || "Terjadi kesalahan pada server"));
                resetButton(originalText);
            }
        } catch (error) {
            console.error('Error:', error);
            await W2HDialog.error("Koneksi terputus atau server bermasalah.");
            resetButton(originalText);
        }
    });
}

function resetButton(text) {
    if (submitBtnText) {
        submitBtnText.innerText = text;
    }

    syncSubmitButtonState();
}

function initializeForm() {
    initializeDocumentValidation();

    submitAttempted = false;

    if (alamatInput) {
        alamatInput.addEventListener('input', () => {
            if (submitAttempted) {
                validateFormState(true);
            } else {
                setAddressError('');
                syncSubmitButtonState();
            }
        });

        alamatInput.addEventListener('blur', () => {
            if (submitAttempted) {
                validateAddress(true);
            }
        });
    }

    packageRadios.forEach(radio => {
        radio.addEventListener('change', updatePackageUi);
    });

    setAddressError('');
    documentFields.forEach(field => {
        const input = document.getElementById(field.id);
        if (input) {
            setDocumentError(input, '');
        }
    });

    updatePackageUi();
    syncSubmitButtonState();

    // 4. Intercept navigasi pindah halaman (klik link) menggunakan event delegation
    document.body.addEventListener('click', async (e) => {
        const link = e.target.closest('a');
        if (link) {
            const href = link.getAttribute('href');
            // Pastikan link valid dan bukan action di halaman yang sama
            if (href && href !== '#' && !href.startsWith('javascript:') && !href.startsWith('mailto:')) {
                // Jika sudah mulai submit, biarkan navigasi
                if (submitAttempted) return;

                // Cegah link diklik langsung
                e.preventDefault();
                
                // Tampilkan alert W2HDialog
                if (typeof W2HDialog !== 'undefined' && typeof W2HDialog.confirm === 'function') {
                    const confirmLeave = await W2HDialog.confirm("Data pada form ini tidak akan tersimpan. Yakin ingin meninggalkan halaman?");
                    if (confirmLeave) {
                        window.location.href = href;
                    }
                } else {
                    // Fallback browser native dialog
                    if (confirm("Data pada form ini tidak akan tersimpan. Yakin ingin meninggalkan halaman?")) {
                        window.location.href = href;
                    }
                }
            }
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeForm);
} else {
    initializeForm();
}
