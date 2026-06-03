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
    const negotiationMessageInput = modal.querySelector('.dashboard-review-negotiation-message');
    const costInput = modal.querySelector('#dashboard-review-cost');
    const negotiateButton = modal.querySelector('#dashboard-review-negotiate-btn');
    const takeRenovationButton = modal.querySelector('#dashboard-review-take-btn');
    const feedbackInput = modal.querySelector('.dashboard-review-feedback-readonly');
    const requestIdEl = modal.querySelector('#dashboard-review-request-id');
    const applicantEl = modal.querySelector('#dashboard-review-applicant');
    const locationEl = modal.querySelector('#dashboard-review-location');
    const budgetEl = modal.querySelector('#dashboard-review-budget');
    const descriptionEl = modal.querySelector('#dashboard-review-description');
    const photoCountEl = modal.querySelector('#dashboard-review-photo-count');
    const galleryEl = modal.querySelector('#dashboard-review-gallery');
    const materialSearchInput = modal.querySelector('#dashboard-review-material-search');
    const materialSourceListEl = modal.querySelector('#dashboard-review-material-source-list');
    const materialSelectedListEl = modal.querySelector('#dashboard-review-material-selected-list');
    const materialEmptyEl = modal.querySelector('#dashboard-review-material-empty');
    const materialTotalEl = modal.querySelector('#dashboard-review-material-total');
    const negotiationListEl = modal.querySelector('#dashboard-review-negotiation-list');
    const focusableSelector = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
    let lastTrigger = null;
    let selectedRequestId = null;
    let selectedRequestDbId = null;
    let selectedRequestCanSendNegotiation = false;
    let selectedRequestCanTakeRenovation = false;
    const selectedMaterialQty = {};

    const formatRupiah = (value) => `Rp ${Number(value || 0).toLocaleString('id-ID')}`;
    const parseRupiahDigits = (value) => String(value || '').replace(/\D+/g, '');
    const formatCostInput = (value) => {
        const digits = parseRupiahDigits(value);

        if (!digits) {
            return '';
        }

        return Number(digits).toLocaleString('id-ID');
    };
    const getMandorCost = () => Number(parseRupiahDigits(costInput instanceof HTMLInputElement ? costInput.value : ''));

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

    const buildNegotiationItem = (message) => {
        const sender = message.pengirim === 'mandor' ? 'Mandor' : 'Customer';
        const nominal = message.nominal_tawaran
            ? `<p class="dashboard-review-material-price">Nominal: ${message.nominal_tawaran}</p>`
            : '';
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
    };

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

    const getMaterialSearchQuery = () =>
        materialSearchInput instanceof HTMLInputElement
            ? materialSearchInput.value.trim().toLowerCase()
            : '';

    const getFilteredMaterials = () => {
        const query = getMaterialSearchQuery();

        if (!query) {
            return materialCatalog.slice(0, 3);
        }

        return materialCatalog.filter((material) => {
            const name = String(material.nama_material || '').toLowerCase();
            const description = String(material.deskripsi || '').toLowerCase();
            const category = String(material.kategori || '').toLowerCase();
            return name.includes(query) || description.includes(query) || category.includes(query);
        });
    };

    const renderMaterialSourceList = () => {
        if (!materialSourceListEl) {
            return;
        }

        const filteredMaterials = getFilteredMaterials();
        materialSourceListEl.innerHTML = '';

        if (!filteredMaterials.length) {
            materialSourceListEl.innerHTML =
                '<p class="dashboard-review-material-not-found">Material tidak ditemukan.</p>';
            return;
        }

        filteredMaterials.forEach((material) => {
            const qty = Number(selectedMaterialQty[material.id] || 0);
            const itemEl = document.createElement('button');
            itemEl.type = 'button';
            itemEl.className = `dashboard-review-material-source-item${qty > 0 ? ' is-selected' : ''}`;
            itemEl.dataset.materialPick = String(material.id);
            itemEl.innerHTML = `
                <div class="dashboard-review-material-main">
                    <p class="dashboard-review-material-name">${material.nama_material || '-'}</p>
                    <p class="dashboard-review-material-meta">${material.deskripsi || '-'}</p>
                    <p class="dashboard-review-material-price">${formatRupiah(material.harga || 0)} / ${material.satuan || '-'}</p>
                </div>
                <span class="dashboard-review-material-source-badge">${qty > 0 ? 'Dipilih' : 'Tambah'}</span>
            `;

            materialSourceListEl.appendChild(itemEl);
        });
    };

    const renderSelectedMaterialList = () => {
        if (!materialSelectedListEl) {
            return;
        }

        const { selectedMaterials } = getMaterialSummary();
        materialSelectedListEl.innerHTML = '';

        if (!selectedMaterials.length) {
            if (materialEmptyEl) {
                materialEmptyEl.hidden = false;
            }
            materialSelectedListEl.hidden = true;
            return;
        }

        if (materialEmptyEl) {
            materialEmptyEl.hidden = true;
        }
        materialSelectedListEl.hidden = false;

        selectedMaterials.forEach((material) => {
            const itemEl = document.createElement('div');
            itemEl.className = 'dashboard-review-material-item';

            const metaEl = document.createElement('div');
            metaEl.className = 'dashboard-review-material-main';
            metaEl.innerHTML = `
                <p class="dashboard-review-material-name">${material.nama_material || '-'}</p>
                <p class="dashboard-review-material-price">${formatRupiah(material.harga || 0)} / ${material.satuan || '-'}</p>
                <p class="dashboard-review-material-meta">Subtotal: ${formatRupiah(material.subtotal || 0)}</p>
            `;

            const counterEl = document.createElement('div');
            counterEl.className = 'dashboard-review-material-counter';
            counterEl.innerHTML = `
                <button type="button" class="dashboard-review-material-counter-btn" data-material-action="decrease" data-material-id="${material.id}">-</button>
                <span class="dashboard-review-material-counter-value" data-material-qty="${material.id}">${material.qty}</span>
                <button type="button" class="dashboard-review-material-counter-btn" data-material-action="increase" data-material-id="${material.id}">+</button>
            `;

            itemEl.appendChild(metaEl);
            itemEl.appendChild(counterEl);
            materialSelectedListEl.appendChild(itemEl);
        });
    };

    const refreshMaterialUI = () => {
        renderMaterialSourceList();
        renderSelectedMaterialList();
        updateMaterialTotals();
    };

    const updateMaterialTotals = () => {
        const { total } = getMaterialSummary();

        if (materialTotalEl) {
            materialTotalEl.textContent = formatRupiah(total);
        }

        updateActionButtonsState();
    };

    const resetMaterialSelection = () => {
        materialCatalog.forEach((material) => {
            selectedMaterialQty[material.id] = 0;
        });

        if (materialSearchInput instanceof HTMLInputElement) {
            materialSearchInput.value = '';
        }

        refreshMaterialUI();
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
            .map((message) => buildNegotiationItem(message))
            .join('');
    };

    const appendNegotiationMessage = (message) => {
        if (!negotiationListEl) {
            return;
        }

        const emptyState = negotiationListEl.querySelector('p');
        if (emptyState && negotiationListEl.childElementCount === 1) {
            negotiationListEl.innerHTML = '';
        }

        negotiationListEl.insertAdjacentHTML('beforeend', buildNegotiationItem(message));
    };

    const populateModal = (requestId) => {
        const request = requestMap[requestId];

        if (!request) {
            return;
        }

        selectedRequestId = request.id;
        selectedRequestDbId = request.db_id ? String(request.db_id) : null;
        selectedRequestCanSendNegotiation = Boolean(request.can_send_negotiation);
        selectedRequestCanTakeRenovation = Boolean(request.can_take_renovation);

        if (requestIdEl) {
            requestIdEl.textContent = `#${request.id}`;
        }

        if (applicantEl) {
            applicantEl.textContent = request.applicant_name || '-';
        }

        if (locationEl) {
            locationEl.textContent = request.location || '-';
        }

        if (budgetEl) {
            budgetEl.textContent = request.budget ? request.budget : '-';
        }

        if (descriptionEl) {
            descriptionEl.textContent = request.description || '-';
        }

        if (feedbackInput instanceof HTMLTextAreaElement) {
            feedbackInput.value = request.existing_offer_feedback || '';
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

        if (negotiationMessageInput instanceof HTMLTextAreaElement) {
            negotiationMessageInput.value = '';
            negotiationMessageInput.disabled = !selectedRequestCanSendNegotiation;
            negotiationMessageInput.placeholder = selectedRequestCanSendNegotiation
                ? 'Tulis balasan negosiasi untuk customer...'
                : 'Balasan negosiasi aktif jika customer sudah mengirim nego.';
        }

        if (costInput instanceof HTMLInputElement) {
            costInput.value = request.existing_offer_cost ? formatCostInput(request.existing_offer_cost) : '';
        }

        if (takeRenovationButton instanceof HTMLButtonElement) {
            takeRenovationButton.disabled = !selectedRequestCanTakeRenovation;
            takeRenovationButton.setAttribute('aria-disabled', String(!selectedRequestCanTakeRenovation));
            takeRenovationButton.dataset.requestId = request.id;
            takeRenovationButton.dataset.requestDbId = String(request.db_id || '');
        }

        if (negotiateButton instanceof HTMLButtonElement) {
            negotiateButton.disabled = !selectedRequestCanSendNegotiation;
            negotiateButton.setAttribute('aria-disabled', String(!selectedRequestCanSendNegotiation));
            negotiateButton.dataset.requestId = request.id;
            negotiateButton.dataset.requestDbId = String(request.db_id || '');
        }

        resetMaterialSelection();
        if (Array.isArray(request.existing_offer_materials)) {
            request.existing_offer_materials.forEach((item) => {
                if (!item || !item.material_id) {
                    return;
                }
                selectedMaterialQty[String(item.material_id)] = Number(item.jumlah || 0);
            });
            refreshMaterialUI();
        }
        renderNegotiationList(request.negotiation_messages);
        updateActionButtonsState();
    };

    const updateActionButtonsState = () => {
        const feedbackValue = feedbackInput instanceof HTMLTextAreaElement ? feedbackInput.value.trim() : '';
        const messageValue = negotiationMessageInput instanceof HTMLTextAreaElement ? negotiationMessageInput.value.trim() : '';
        const isCostValid = getMandorCost() > 0;
        const { itemCount } = getMaterialSummary();
        const hasMaterialSelected = itemCount > 0;
        const hasRequestSelected = typeof selectedRequestDbId === 'string' && selectedRequestDbId.length > 0;
        const canTakeRenovation = selectedRequestCanTakeRenovation && feedbackValue.length > 0 && isCostValid && hasMaterialSelected && hasRequestSelected;
        const canSendNegotiation = selectedRequestCanSendNegotiation && messageValue.length > 0 && isCostValid && hasMaterialSelected && hasRequestSelected;

        if (takeRenovationButton instanceof HTMLButtonElement) {
            takeRenovationButton.disabled = !canTakeRenovation;
            takeRenovationButton.setAttribute('aria-disabled', String(!canTakeRenovation));
        }

        if (negotiateButton instanceof HTMLButtonElement) {
            negotiateButton.disabled = !canSendNegotiation;
            negotiateButton.setAttribute('aria-disabled', String(!canSendNegotiation));
        }
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

    if (costInput instanceof HTMLInputElement) {
        costInput.addEventListener('input', () => {
            const selectionStart = costInput.selectionStart;
            costInput.value = formatCostInput(costInput.value);

            if (selectionStart !== null) {
                costInput.setSelectionRange(costInput.value.length, costInput.value.length);
            }

            updateActionButtonsState();
        });

        costInput.addEventListener('focus', () => {
            costInput.value = parseRupiahDigits(costInput.value);
        });

        costInput.addEventListener('blur', () => {
            costInput.value = formatCostInput(costInput.value);
        });
    }

    if (negotiationMessageInput instanceof HTMLTextAreaElement) {
        negotiationMessageInput.addEventListener('input', updateActionButtonsState);
    }

    if (negotiateButton instanceof HTMLButtonElement) {
        negotiateButton.addEventListener('click', async () => {
            const requestId = negotiateButton.dataset.requestId;
            const requestDbId = negotiateButton.dataset.requestDbId;
            const mandorCost = getMandorCost();
            const negotiationMessage = negotiationMessageInput instanceof HTMLTextAreaElement ? negotiationMessageInput.value.trim() : '';
            const { selectedMaterials, total: materialTotal } = getMaterialSummary();

            if (!requestId || !requestDbId) {
                return;
            }

            if (!negotiationMessage) {
                await W2HDialog.alert('Pesan negosiasi tidak boleh kosong.');
                return;
            }

            if (!selectedMaterials.length || mandorCost <= 0) {
                await W2HDialog.alert('Pilih material dan isi nominal renovasi terlebih dahulu.');
                return;
            }

            const payload = {
                pesan: negotiationMessage,
                estimasi_biaya: mandorCost,
                materials: selectedMaterials.map((material) => ({
                    material_id: Number(material.id),
                    jumlah: Number(material.qty),
                })),
            };

            const result = await postJson(`/mandor/renovation/${requestDbId}/negotiate`, payload);

            if (!result.ok) {
                await W2HDialog.error(result.data.message || 'Gagal mengirim negosiasi.');
                return;
            }

            const createdMessage = result.data.negotiation || {
                pengirim: 'mandor',
                pesan: negotiationMessage,
                nominal_tawaran: formatRupiah(mandorCost),
                waktu: result.data.waktu || '',
            };

            appendNegotiationMessage(createdMessage);
            const request = requestMap[requestId];
            if (request) {
                request.negotiation_messages = Array.isArray(request.negotiation_messages)
                    ? [...request.negotiation_messages, createdMessage]
                    : [createdMessage];
            }
            if (negotiationMessageInput instanceof HTMLTextAreaElement) {
                negotiationMessageInput.value = '';
            }
            updateActionButtonsState();
            await W2HDialog.success(result.data.message || 'Negosiasi berhasil dikirim.');
            closeModal();
        });
    }

    if (takeRenovationButton instanceof HTMLButtonElement) {
        takeRenovationButton.addEventListener('click', async () => {
            const requestId = takeRenovationButton.dataset.requestId;
            const requestDbId = takeRenovationButton.dataset.requestDbId;
            const mandorCost = getMandorCost();
            const feedbackValue = feedbackInput instanceof HTMLTextAreaElement ? feedbackInput.value.trim() : '';
            const { selectedMaterials } = getMaterialSummary();

            if (!requestId || !requestDbId) {
                return;
            }

            if (!feedbackValue) {
                await W2HDialog.alert('Feedback mandor tidak boleh kosong.');
                return;
            }

            if (!selectedMaterials.length || mandorCost <= 0) {
                await W2HDialog.alert('Pilih material dan isi nominal renovasi terlebih dahulu.');
                return;
            }

            takeRenovationButton.classList.add('is-loading');
            takeRenovationButton.disabled = true;
            takeRenovationButton.setAttribute('aria-disabled', 'true');

            try {
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
                    takeRenovationButton.classList.remove('is-loading');
                    updateActionButtonsState();
                    await W2HDialog.error(result.data.message || 'Gagal mengambil renovasi.');
                    return;
                }

                const createdMessage = {
                    pengirim: 'mandor',
                    pesan: feedbackValue,
                    nominal_tawaran: formatRupiah(mandorCost),
                    waktu: new Intl.DateTimeFormat('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                    }).format(new Date()),
                };

                appendNegotiationMessage(createdMessage);
                const request = requestMap[requestId];
                if (request) {
                    request.existing_offer_feedback = feedbackValue;
                    request.existing_offer_cost = mandorCost;
                    request.existing_offer_materials = selectedMaterials.map((material) => ({
                        material_id: String(material.id),
                        jumlah: Number(material.qty),
                    }));
                    request.existing_offer_status = 'pending';
                    request.negotiation_messages = Array.isArray(request.negotiation_messages)
                        ? [...request.negotiation_messages, createdMessage]
                        : [createdMessage];
                    request.can_take_renovation = false;
                }
                selectedRequestCanTakeRenovation = false;
                takeRenovationButton.classList.remove('is-loading');
                updateActionButtonsState();
                await W2HDialog.success(result.data.message || 'Renovasi berhasil diambil.');
                // Close the review modal after the user acknowledges the success alert
                closeModal();
            } catch (error) {
                takeRenovationButton.classList.remove('is-loading');
                updateActionButtonsState();
                await W2HDialog.error('Terjadi kesalahan server.');
            }
        });
    }

    if (materialSearchInput instanceof HTMLInputElement) {
        materialSearchInput.addEventListener('input', renderMaterialSourceList);
    }

    if (materialSourceListEl) {
        materialSourceListEl.addEventListener('click', (event) => {
            const button = event.target.closest('[data-material-pick]');
            if (!button) {
                return;
            }

            const materialId = button.getAttribute('data-material-pick');
            if (!materialId || !(materialId in selectedMaterialQty)) {
                return;
            }

            if (Number(selectedMaterialQty[materialId] || 0) <= 0) {
                selectedMaterialQty[materialId] = 1;
            }

            refreshMaterialUI();
        });
    }

    if (materialSelectedListEl) {
        materialSelectedListEl.addEventListener('click', (event) => {
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

            refreshMaterialUI();
        });
    }

    renderMaterialSourceList();
    resetMaterialSelection();
    updateActionButtonsState();

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
