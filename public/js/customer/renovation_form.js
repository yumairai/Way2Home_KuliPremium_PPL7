(function () {
    const isTester = window.W2H_IS_TESTER === true;

    const MAX_FILES = 6;
    const MIN_FILES = 1;
    const MAX_SIZE = 2 * 1024 * 1024;

    const uploadBox = document.querySelector('.rf-upload-box');
    const input = document.getElementById('foto_detail_input');
    const previewGrid = document.querySelector('.rf-preview-grid');
    let previewItems = previewGrid ? Array.from(previewGrid.querySelectorAll('.rf-preview-item')) : [];
    const fotoError = document.getElementById('foto-error');
    const form = document.querySelector('.rf-form-body');
    const budgetInput = document.querySelector('.rf-input-budget');
    const submitBtn = document.getElementById('rf-submit-btn');

    if (!uploadBox || !input || !previewGrid) return;

    function getBudgetDigits(value) {
        return String(value || '').replace(/\D+/g, '');
    }

    function formatBudget(value) {
        const digits = getBudgetDigits(value);
        if (!digits) return '';
        return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function syncBudgetInput() {
        if (!budgetInput) return;
        budgetInput.value = formatBudget(budgetInput.value);
    }

    if (previewItems.length === 0) {
        for (let i = 0; i < MAX_FILES; i++) {
            const item = document.createElement('div');
            item.className = 'rf-preview-item';

            const img = document.createElement('img');
            img.className = 'rf-preview-image';
            img.alt = `Preview ${i + 1}`;
            img.dataset.placeholder = '';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'rf-preview-remove-btn';
            btn.style.display = 'none';

            const span = document.createElement('span');
            span.className = 'material-symbols-outlined rf-preview-remove-icon';
            span.dataset.icon = 'close';
            span.textContent = 'close';

            btn.appendChild(span);
            item.appendChild(img);
            item.appendChild(btn);
            previewGrid.appendChild(item);
        }

        previewItems = Array.from(previewGrid.querySelectorAll('.rf-preview-item'));
    }

    input.classList.add('rf-file-input-hidden');

    if (budgetInput) {
        syncBudgetInput();
        budgetInput.addEventListener('input', () => syncBudgetInput());
        budgetInput.addEventListener('blur', () => syncBudgetInput());
    }

    let files = [];

    // ── Tester dummy setup ────────────────────────────────────────────
    let dummyItem = null;
    if (isTester) {
        const dummyUrl = 'https://ovyjfudrdwrlyioygotq.supabase.co/storage/v1/object/public/public-assets/testing/renovasi/foto_renovasi.jpg';

        dummyItem = document.createElement('div');
        dummyItem.className = 'rf-preview-item';
        dummyItem.id = 'tester-dummy-preview';

        const dummyImg = document.createElement('img');
        dummyImg.className = 'rf-preview-image';
        dummyImg.src = dummyUrl;
        dummyImg.alt = 'Foto tester';

        dummyItem.appendChild(dummyImg);
        // Tidak ada tombol hapus — sengaja
    }

    function setError(msg) {
        if (fotoError) fotoError.textContent = msg || '';
        uploadBox.classList.toggle('has-error', Boolean(msg));
    }

    function syncNativeInput() {
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        input.files = dt.files;
    }

    function renderPreviews() {
        previewItems.forEach((item, idx) => {
            const img = item.querySelector('.rf-preview-image');
            const btn = item.querySelector('.rf-preview-remove-btn');

            if (idx < files.length) {
                const file = files[idx];
                img.src = URL.createObjectURL(file);
                img.alt = file.name;
                item.style.display = '';
                btn.style.display = '';
                btn.dataset.index = idx;
            } else {
                const original = img.dataset.placeholder;
                if (original) {
                    img.src = original;
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
                btn.style.display = 'none';
            }
        });

        // Tester: dummy muncul kalau tidak ada file, hilang kalau ada file
        if (isTester && dummyItem) {
            if (files.length > 0) {
                dummyItem.style.display = 'none';
            } else {
                dummyItem.style.display = '';
                if (!previewGrid.contains(dummyItem)) {
                    previewGrid.prepend(dummyItem);
                }
            }
        }
    }

    previewItems.forEach(item => {
        const img = item.querySelector('.rf-preview-image');
        if (img && !img.dataset.placeholder) img.dataset.placeholder = img.src || '';
    });

    function addFiles(fileList) {
        setError('');
        const incoming = Array.from(fileList || []);
        for (const f of incoming) {
            if (files.length >= MAX_FILES) break;
            if (!f.type.startsWith('image/')) continue;
            if (f.size > MAX_SIZE) {
                setError(`${f.name} gagal diupload, file melebihi batas maksimum 2 MB.`);
                continue;
            }
            files.push(f);
        }
        syncNativeInput();
        renderPreviews();
    }

    uploadBox.addEventListener('click', (e) => {
        if (e.target.classList.contains('rf-preview-remove-btn')) return;
        // Jangan buka file dialog kalau klik dummy
        if (isTester && dummyItem && dummyItem.contains(e.target)) return;
        input.click();
    });

    input.addEventListener('change', (e) => {
        addFiles(e.target.files);
    });

    ['dragenter', 'dragover'].forEach(ev => {
        uploadBox.addEventListener(ev, (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadBox.classList.add('is-dragging');
        });
    });

    ['dragleave', 'drop'].forEach(ev => {
        uploadBox.addEventListener(ev, (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadBox.classList.remove('is-dragging');
        });
    });

    uploadBox.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length) {
            addFiles(dt.files);
        }
    });

    previewGrid.addEventListener('click', (e) => {
        const btn = e.target.closest('.rf-preview-remove-btn');
        if (!btn) return;
        const idx = Number(btn.dataset.index);
        if (Number.isFinite(idx) && idx >= 0 && idx < files.length) {
            files.splice(idx, 1);
            renderPreviews();
            syncNativeInput();
        }
    });

    if (form) {
        form.addEventListener('submit', (e) => {
            if (isTester) {
                if (submitBtn) {
                    submitBtn.classList.add('is-loading');
                    submitBtn.disabled = true;
                }
                return true;
            }

            if (files.length < MIN_FILES) {
                e.preventDefault();
                setError('Mohon unggah minimal 1 gambar kerusakan.');
                uploadBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }

            if (submitBtn) {
                submitBtn.classList.add('is-loading');
                submitBtn.disabled = true;
            }
            return true;
        });
    }

    renderPreviews();
})();