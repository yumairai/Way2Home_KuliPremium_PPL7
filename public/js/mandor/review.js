document.addEventListener('DOMContentLoaded', () => {
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';
    const modal = document.getElementById('dashboard-review-modal');
    const requestMap = window.renovationRequestMap || {};
    const materialCatalog = Array.isArray(window.renovationMaterialCatalog) ? window.renovationMaterialCatalog : [];

    if (!modal) {
        return;
    }

    const openButtons = document.querySelectorAll('[data-review-open]');
    const closeButtons = modal.querySelectorAll('[data-review-close]');
    const feedbackInput = modal.querySelector('.dashboard-review-feedback');
    const costInput = modal.querySelector('#dashboard-review-cost');
    const takeRenovationButton = modal.querySelector('#dashboard-review-take-btn');
    const requestIdEl = modal.querySelector('#dashboard-review-request-id');
    const applicantEl = modal.querySelector('#dashboard-review-applicant');
    const locationEl = modal.querySelector('#dashboard-review-location');
    const descriptionEl = modal.querySelector('#dashboard-review-description');
    const photoCountEl = modal.querySelector('#dashboard-review-photo-count');
    const galleryEl = modal.querySelector('#dashboard-review-gallery');
    const materialListEl = modal.querySelector('#dashboard-review-material-list');
    const materialTotalEl = modal.querySelector('#dashboard-review-material-total');
    const negotiationListEl = modal.querySelector('#dashboard-review-negotiation-list');
    const focusableSelector = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
    let lastTrigger = null;
    let selectedRequestId = null;
    const selectedMaterialQty = {};

    const formatRupiah = (value) => `Rp ${Number(value || 0).toLocaleString('id-ID')}`;

    const postJson = (url, payload) =>
        fetch(url, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
            },
            body: JSON.stringify(payload),
        }).then(async (response) => {
            const data = await response.json().catch(() => ({}));
            return { ok: response.ok, data };
        });

    const getMaterialSummary = () => {
        const selectedMaterials = materialCatalog
            .map((material) => {
                const qty = Number(selectedMaterialQty[material.id] || 0);
                if (qty <= 0) {
                    return null;
                }

                const price = Number(material.harga || 0);
                return {
                    ...material,
                    qty,
                    subtotal: qty * price,
                };
            })
            .filter(Boolean);

        const total = selectedMaterials.reduce((sum, material) => sum + material.subtotal, 0);
        const itemCount = selectedMaterials.reduce((sum, material) => sum + material.qty, 0);

        return { selectedMaterials, total, itemCount };
    };

    const renderMaterialList = () => {
        if (!materialListEl) {
            return;
        }

        materialListEl.innerHTML = '';

        materialCatalog.forEach((material) => {
            const qty = Number(selectedMaterialQty[material.id] || 0);

            const itemEl = document.createElement('div');
            itemEl.className = 'dashboard-review-material-item';

            const metaEl = document.createElement('div');
            metaEl.className = 'dashboard-review-material-main';
            metaEl.innerHTML = `
                <p class="dashboard-review-material-name">${material.nama_material || '-'}</p>
                <p class="dashboard-review-material-meta">${material.deskripsi || '-'}</p>
                <p class="dashboard-review-material-price">Harga: ${formatRupiah(material.harga || 0)} / ${material.satuan || '-'}</p>
            `;

            const counterEl = document.createElement('div');
            counterEl.className = 'dashboard-review-material-counter';
            counterEl.innerHTML = `
                <button type="button" class="dashboard-review-material-counter-btn" data-material-action="decrease" data-material-id="${material.id}">-</button>
                <span class="dashboard-review-material-counter-value" data-material-qty="${material.id}">${qty}</span>
                <button type="button" class="dashboard-review-material-counter-btn" data-material-action="increase" data-material-id="${material.id}">+</button>
            `;

            itemEl.appendChild(metaEl);
            itemEl.appendChild(counterEl);
            materialListEl.appendChild(itemEl);
        });
    };

    const updateMaterialTotals = () => {
        const { total } = getMaterialSummary();

        if (materialTotalEl) {
            materialTotalEl.textContent = formatRupiah(total);
        }

        updateTakeRenovationButtonState();
    };

    const resetMaterialSelection = () => {
        materialCatalog.forEach((material) => {
            selectedMaterialQty[material.id] = 0;
        });
        renderMaterialList();
        updateMaterialTotals();
    };

    const renderNegotiationList = (messages) => {
        if (!negotiationListEl) {
            return;
        }

        const safeMessages = Array.isArray(messages) ? messages : [];
        if (!safeMessages.length) {
            negotiationListEl.innerHTML = '<p style="margin:0;color:var(--on-surface-variant);">Belum ada negosiasi dari customer.</p>';
            return;
        }

        negotiationListEl.innerHTML = safeMessages
            .map((message) => {
                const sender = message.pengirim === 'customer' ? 'Customer' : 'Mandor';
                const nominal = message.nominal_tawaran ? `<p class="dashboard-review-material-price">Nominal: ${message.nominal_tawaran}</p>` : '';
                const time = message.waktu ? `<p class="dashboard-review-material-meta">${message.waktu}</p>` : '';
                return `
                    <div class="dashboard-review-material-item">
                        <div class="dashboard-review-material-main">
                            <p class="dashboard-review-material-name">${sender}</p>
                            ${time}
                            <p class="dashboard-review-material-meta">${message.pesan || '-'}</p>
                            ${nominal}
                        </div>
                    </div>
                `;
            })
            .join('');
    };

    const populateModal = (requestId) => {
        const request = requestMap[requestId];

        if (!request) {
            return;
        }

        selectedRequestId = request.id;

        if (requestIdEl) {
            requestIdEl.textContent = `#${request.id}`;
        }

        if (applicantEl) {
            applicantEl.textContent = request.applicant_name || '-';
        }

        if (locationEl) {
            locationEl.textContent = request.location || '-';
        }

        if (descriptionEl) {
            descriptionEl.textContent = request.description || '-';
        }

        const photos = Array.isArray(request.photos) ? request.photos : [];

        if (photoCountEl) {
            photoCountEl.textContent = String(photos.length);
        }

        if (galleryEl) {
            galleryEl.innerHTML = '';

            photos.forEach((photoUrl, index) => {
                const photoWrap = document.createElement('div');
                photoWrap.className = 'dashboard-review-photo';

                const image = document.createElement('img');
                image.src = photoUrl;
                image.alt = `Foto kerusakan ${index + 1} untuk #${request.id}`;

                photoWrap.appendChild(image);
                galleryEl.appendChild(photoWrap);
            });
        }

        if (feedbackInput instanceof HTMLTextAreaElement) {
            feedbackInput.value = request.existing_offer_feedback || '';
        }

        if (costInput instanceof HTMLInputElement) {
            costInput.value = request.existing_offer_cost ? String(request.existing_offer_cost) : '';
        }

        if (takeRenovationButton instanceof HTMLButtonElement) {
            takeRenovationButton.dataset.requestId = request.id;
            takeRenovationButton.dataset.requestDbId = String(request.db_id || '');
        }

        resetMaterialSelection();
        if (Array.isArray(request.existing_offer_materials)) {
            request.existing_offer_materials.forEach((item) => {
                if (!item || !item.material_id) {
                    return;
                }
                selectedMaterialQty[String(item.material_id)] = Number(item.jumlah || 0);
            });
            renderMaterialList();
            updateMaterialTotals();
        }
        renderNegotiationList(request.negotiation_messages);
        updateTakeRenovationButtonState();
    };

    const updateTakeRenovationButtonState = () => {
        if (!(takeRenovationButton instanceof HTMLButtonElement)) {
            return;
        }

        const feedbackValue = feedbackInput instanceof HTMLTextAreaElement ? feedbackInput.value.trim() : '';
        const costValue = costInput instanceof HTMLInputElement ? costInput.value.trim() : '';
        const isCostValid = Number(costValue) > 0;
        const { itemCount } = getMaterialSummary();
        const hasMaterialSelected = itemCount > 0;
        const hasRequestSelected = typeof selectedRequestId === 'string' && selectedRequestId.length > 0;
        const isFormComplete = feedbackValue.length > 0 && isCostValid && hasMaterialSelected && hasRequestSelected;

        takeRenovationButton.disabled = !isFormComplete;
        takeRenovationButton.setAttribute('aria-disabled', String(!isFormComplete));
    };

    const setModalState = (isOpen) => {
        modal.hidden = !isOpen;
        modal.setAttribute('aria-hidden', String(!isOpen));
        document.body.classList.toggle('dashboard-modal-open', isOpen);

        if (isOpen) {
            const firstFocusable = modal.querySelector(focusableSelector);
            if (firstFocusable) {
                firstFocusable.focus();
            }
            return;
        }

        if (lastTrigger instanceof HTMLElement) {
            lastTrigger.focus();
        }
    };

    const openModal = (trigger) => {
        lastTrigger = trigger instanceof HTMLElement ? trigger : null;

        if (trigger instanceof HTMLElement) {
            const requestId = trigger.getAttribute('data-request-id');
            if (requestId) {
                populateModal(requestId);
            }
        }

        setModalState(true);
    };

    const closeModal = () => {
        setModalState(false);
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', () => openModal(button));
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    if (feedbackInput instanceof HTMLTextAreaElement) {
        feedbackInput.addEventListener('input', updateTakeRenovationButtonState);
    }

    if (costInput instanceof HTMLInputElement) {
        costInput.addEventListener('input', updateTakeRenovationButtonState);
    }

    if (takeRenovationButton instanceof HTMLButtonElement) {
        takeRenovationButton.addEventListener('click', async () => {
            const requestId = takeRenovationButton.dataset.requestId;
            const requestDbId = takeRenovationButton.dataset.requestDbId;
            const mandorCost = Number(costInput instanceof HTMLInputElement ? costInput.value : 0);
            const feedbackValue = feedbackInput instanceof HTMLTextAreaElement ? feedbackInput.value.trim() : '';
            const { selectedMaterials, total: materialTotal } = getMaterialSummary();

            if (!requestId || !requestDbId) {
                return;
            }

            const materialLines = selectedMaterials.map((material) => {
                return `- ${material.nama_material}: ${material.qty} ${material.satuan} (${formatRupiah(material.subtotal)})`;
            });

            const grandTotal = materialTotal + mandorCost;

            const payload = {
                feedback: feedbackValue,
                estimasi_biaya: mandorCost,
                materials: selectedMaterials.map((material) => ({
                    material_id: Number(material.id),
                    jumlah: Number(material.qty),
                })),
            };

            const result = await postJson(`/mandor/renovation/${requestDbId}/offer`, payload);

            if (!result.ok) {
                alert(result.data.message || 'Gagal menyimpan penawaran renovasi.');
                return;
            }

            const messageLines = [
                `Ringkasan Biaya untuk #${requestId}`,
                '',
                'Material:',
                ...(materialLines.length ? materialLines : ['- Belum ada material']),
                `Total Material: ${formatRupiah(materialTotal)}`,
                `Biaya Renovasi Mandor: ${formatRupiah(mandorCost)}`,
                `Total Keseluruhan: ${formatRupiah(grandTotal)}`,
                '',
                result.data.message || 'Penawaran berhasil dikirim.',
            ];

            alert(messageLines.join('\n'));
            window.location.href = takeRenovationButton.dataset.trackingUrl || '/mandor/tracking';
        });
    }

    if (materialListEl) {
        materialListEl.addEventListener('click', (event) => {
            const button = event.target.closest('[data-material-action]');
            if (!button) {
                return;
            }

            const materialId = button.getAttribute('data-material-id');
            const action = button.getAttribute('data-material-action');

            if (!materialId || !(materialId in selectedMaterialQty)) {
                return;
            }

            const currentQty = Number(selectedMaterialQty[materialId] || 0);

            if (action === 'increase') {
                selectedMaterialQty[materialId] = currentQty + 1;
            }

            if (action === 'decrease') {
                selectedMaterialQty[materialId] = Math.max(0, currentQty - 1);
            }

            const qtyEl = materialListEl.querySelector(`[data-material-qty="${materialId}"]`);
            if (qtyEl) {
                qtyEl.textContent = String(selectedMaterialQty[materialId]);
            }

            updateMaterialTotals();
        });
    }

    renderMaterialList();
    resetMaterialSelection();
    updateTakeRenovationButtonState();

    modal.addEventListener('click', (event) => {
        if (event.target === modal || event.target.hasAttribute('data-review-close')) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });
});
