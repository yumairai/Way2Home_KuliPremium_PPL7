document.addEventListener('DOMContentLoaded', function () {
    var csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';
    var stateConfig = {
        waiting: {
            label: 'Menunggu Review',
            icon: 'schedule',
            feedbackClass: 'rv-feedback-pending',
            budgetIcon: 'calculate',
        },
        'on-progress': {
            label: 'Renovasi Aktif',
            icon: 'schedule',
            feedbackClass: 'rv-feedback-pending',
            budgetIcon: 'calculate',
        },
        reviewed: {
            label: 'Sudah Direview',
            icon: 'verified',
            feedbackClass: 'rv-feedback-reviewed',
            budgetIcon: 'analytics',
        },
        completed: {
            label: 'Request Renovasi Selesai',
            icon: 'verified',
            feedbackClass: 'rv-feedback-reviewed',
            budgetIcon: 'analytics',
        },
    };

    function setPanelVisibility(cardEl, nextState) {
        var reviewedPanel = cardEl.querySelector('[data-state-panel="reviewed"]');
        var progressPanel = cardEl.querySelector('[data-state-panel="progress"]');

        if (reviewedPanel) {
            reviewedPanel.classList.toggle('rv-hidden', nextState !== 'reviewed');
        }

        if (progressPanel) {
            var isProgressLikeState = nextState === 'on-progress' || nextState === 'completed';
            progressPanel.classList.toggle('rv-hidden', !isProgressLikeState);
        }
    }

    function postJson(url, payload) {
        return fetch(url, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
            },
            body: JSON.stringify(payload || {}),
        }).then(function (response) {
            return response
                .json()
                .catch(function () {
                    return {};
                })
                .then(function (data) {
                    return { ok: response.ok, data: data };
                });
        });
    }

    function applyStateToCard(cardEl, nextState) {
        var config = stateConfig[nextState];
        if (!config) {
            return;
        }

        cardEl.setAttribute('data-request-status', nextState);

        var statusPill = cardEl.querySelector('[data-state-pill]');
        if (statusPill) {
            statusPill.classList.remove('waiting', 'reviewed', 'on-progress', 'completed');
            statusPill.classList.add(nextState);
        }

        var statusIcon = cardEl.querySelector('.rv-status-icon');
        if (statusIcon) {
            statusIcon.textContent = config.icon;
        }

        var statusText = cardEl.querySelector('.rv-status-text');
        if (statusText) {
            statusText.textContent = config.label;
        }

        var feedbackBox = cardEl.querySelector('[data-feedback-box]');
        if (feedbackBox) {
            feedbackBox.classList.remove('rv-feedback-pending', 'rv-feedback-reviewed');
            feedbackBox.classList.add(config.feedbackClass);
        }

        var budgetIcon = cardEl.querySelector('[data-budget-icon]');
        if (budgetIcon) {
            budgetIcon.textContent = config.budgetIcon;
        }

        setPanelVisibility(cardEl, nextState);
    }

    document.addEventListener('click', function (event) {
        var transitionButton = event.target.closest('[data-transition-state]');

        if (transitionButton) {
            event.preventDefault();

            var nextState = transitionButton.getAttribute('data-transition-state');
            var requestCard = transitionButton.closest('.rv-request-card');

            if (requestCard && nextState) {
                if (nextState === 'on-progress') {
                    var requestId = requestCard.getAttribute('data-request-id');
                    var isActionable = transitionButton.getAttribute('data-service-action') === '1';

                    if (!requestId) {
                        alert('Data request tidak valid.');
                        return;
                    }

                    if (!isActionable) {
                        alert('Penawaran jasa sudah tidak tersedia.');
                        return;
                    }

                    postJson('/renovation/' + requestId + '/accept-offer').then(function (result) {
                        if (!result.ok) {
                            alert(result.data.message || 'Gagal mengambil jasa renovasi.');
                            return;
                        }

                        applyStateToCard(requestCard, nextState);
                        window.location.reload();
                    });
                } else if (nextState === 'completed') {
                    alert('Status selesai diperbarui oleh mandor setelah pekerjaan selesai.');
                } else {
                    applyStateToCard(requestCard, nextState);
                }
            }

            return;
        }

        var alertButton = event.target.closest('.js-alert-btn');
        var materialToggleButton = event.target.closest('.js-toggle-material-details');
        var negotiateButton = event.target.closest('.js-negotiate-btn');
        var rejectOfferButton = event.target.closest('.js-reject-offer-btn');

        if (negotiateButton) {
            event.preventDefault();

            var negotiateCard = negotiateButton.closest('.rv-request-card');
            if (!negotiateCard) {
                return;
            }

            var negotiateRequestId = negotiateCard.getAttribute('data-request-id');
            if (!negotiateRequestId) {
                alert('Data request tidak valid.');
                return;
            }

            var messageInput = negotiateCard.querySelector('.js-negotiation-message');
            var priceInput = negotiateCard.querySelector('.js-negotiation-price');
            var messageValue = messageInput ? String(messageInput.value || '').trim() : '';
            var priceValue = priceInput ? String(priceInput.value || '').trim() : '';

            if (messageValue.length < 5) {
                alert('Pesan negosiasi minimal 5 karakter.');
                return;
            }

            postJson('/renovation/' + negotiateRequestId + '/negotiate', {
                pesan: messageValue,
                nominal_tawaran: priceValue !== '' ? Number(priceValue) : null,
            }).then(function (result) {
                if (!result.ok) {
                    alert(result.data.message || 'Gagal mengirim negosiasi.');
                    return;
                }

                alert(result.data.message || 'Negosiasi berhasil dikirim.');
                window.location.reload();
            });
            return;
        }

        if (rejectOfferButton) {
            event.preventDefault();

            var rejectCard = rejectOfferButton.closest('.rv-request-card');
            if (!rejectCard) {
                return;
            }

            var rejectRequestId = rejectCard.getAttribute('data-request-id');
            if (!rejectRequestId) {
                alert('Data request tidak valid.');
                return;
            }

            if (!confirm('Yakin ingin menolak penawaran saat ini?')) {
                return;
            }

            postJson('/renovation/' + rejectRequestId + '/reject-offer', {
                pesan: 'Penawaran ditolak oleh customer.',
            }).then(function (result) {
                if (!result.ok) {
                    alert(result.data.message || 'Gagal menolak penawaran.');
                    return;
                }

                alert(result.data.message || 'Penawaran berhasil ditolak.');
                window.location.reload();
            });
            return;
        }

        if (materialToggleButton) {
            event.preventDefault();

            var materialBox = materialToggleButton.closest('.rv-material-box');
            if (!materialBox) {
                return;
            }

            var detailsEl = materialBox.querySelector('.rv-material-details');
            if (!detailsEl) {
                return;
            }

            var isExpanded = materialToggleButton.getAttribute('aria-expanded') === 'true';
            materialToggleButton.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
            detailsEl.style.display = isExpanded ? 'none' : 'block';
            return;
        }

        if (!alertButton) {
            return;
        }

        event.preventDefault();

        var message = alertButton.getAttribute('data-alert-message') || 'Fitur ini akan segera tersedia.';
        alert(message);
    });

    var detailModal = document.getElementById('requestDetailModal');
    var modalRequestIdEl = detailModal ? detailModal.querySelector('[data-modal-request-id]') : null;
    var modalDescriptionEl = detailModal ? detailModal.querySelector('[data-modal-description]') : null;
    var modalMainPhotoEl = detailModal ? detailModal.querySelector('[data-modal-main-photo]') : null;
    var modalGalleryIndexEl = detailModal ? detailModal.querySelector('[data-gallery-index]') : null;
    var modalGalleryTotalEl = detailModal ? detailModal.querySelector('[data-gallery-total]') : null;
    var modalGalleryThumbsEl = detailModal ? detailModal.querySelector('[data-gallery-thumbs]') : null;
    var modalMaterialListEl = detailModal ? detailModal.querySelector('[data-modal-material-list]') : null;
    var galleryPrevButton = detailModal ? detailModal.querySelector('[data-gallery-nav="prev"]') : null;
    var galleryNextButton = detailModal ? detailModal.querySelector('[data-gallery-nav="next"]') : null;
    var placeholderImage = '/images/aset/user-dummy.jpg';
    var activePhotos = [];
    var activePhotoIndex = 0;
    var activeMaterials = [];

    function setModalOpenState(isOpen) {
        if (!detailModal) {
            return;
        }

        detailModal.classList.toggle('is-open', isOpen);
        detailModal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        document.body.style.overflow = isOpen ? 'hidden' : '';
    }

    function renderGalleryThumbs() {
        if (!modalGalleryThumbsEl) {
            return;
        }

        modalGalleryThumbsEl.innerHTML = '';

        activePhotos.forEach(function (photoPath, index) {
            var thumbButton = document.createElement('button');
            thumbButton.type = 'button';
            thumbButton.className = 'rv-gallery-thumb' + (index === activePhotoIndex ? ' is-active' : '');
            thumbButton.setAttribute('data-gallery-thumb-index', String(index));
            thumbButton.setAttribute('aria-label', 'Lihat foto ' + (index + 1));

            var thumbImage = document.createElement('img');
            thumbImage.src = photoPath;
            thumbImage.alt = 'Thumbnail foto kerusakan ' + (index + 1);

            thumbButton.appendChild(thumbImage);
            modalGalleryThumbsEl.appendChild(thumbButton);
        });
    }

    function renderActivePhoto() {
        if (!modalMainPhotoEl || !modalGalleryIndexEl || !modalGalleryTotalEl) {
            return;
        }

        var totalPhotos = activePhotos.length;
        var safeIndex = Math.min(Math.max(activePhotoIndex, 0), Math.max(totalPhotos - 1, 0));
        activePhotoIndex = safeIndex;

        modalMainPhotoEl.src = activePhotos[safeIndex] || placeholderImage;
        modalMainPhotoEl.alt = 'Foto kerusakan ' + (safeIndex + 1);
        modalGalleryIndexEl.textContent = String(totalPhotos > 0 ? safeIndex + 1 : 0);
        modalGalleryTotalEl.textContent = String(totalPhotos);

        if (galleryPrevButton) {
            galleryPrevButton.disabled = totalPhotos <= 1;
        }

        if (galleryNextButton) {
            galleryNextButton.disabled = totalPhotos <= 1;
        }

        renderGalleryThumbs();
    }

    function parseMaterials(rawMaterials) {
        if (!rawMaterials) {
            return [];
        }

        try {
            var parsed = JSON.parse(rawMaterials);
            if (Array.isArray(parsed)) {
                return parsed;
            }
        } catch (error) {
            return [];
        }

        return [];
    }

    function renderMaterialListInModal() {
        if (!modalMaterialListEl) {
            return;
        }

        modalMaterialListEl.innerHTML = '';

        if (!Array.isArray(activeMaterials) || activeMaterials.length === 0) {
            modalMaterialListEl.innerHTML = '<p class="rv-material-meta">Belum ada list material dari mandor.</p>';
            return;
        }

        var total = 0;

        activeMaterials.forEach(function (material) {
            var qty = Number(material.jumlah || 0);
            var price = Number(material.harga || 0);
            var subtotal = qty * price;
            total += subtotal;

            var item = document.createElement('div');
            item.className = 'rv-material-content';
            item.innerHTML =
                '<p class="rv-material-name">' + (material.nama_material || '-') + '</p>' +
                '<p class="rv-material-meta">Jumlah: ' + qty + ' ' + (material.satuan || '-') + '</p>' +
                '<p class="rv-material-price">Harga Satuan: Rp ' + price.toLocaleString('id-ID') + '</p>' +
                '<p class="rv-material-price">Subtotal: Rp ' + subtotal.toLocaleString('id-ID') + '</p>';

            modalMaterialListEl.appendChild(item);
        });

        var totalEl = document.createElement('div');
        totalEl.className = 'rv-material-total';
        totalEl.innerHTML =
            '<p class="rv-material-total-label">Total Material</p>' +
            '<p class="rv-material-total-value">Rp ' + total.toLocaleString('id-ID') + '</p>';
        modalMaterialListEl.appendChild(totalEl);
    }

    function parsePhotos(rawPhotos) {
        if (!rawPhotos) {
            return [];
        }

        try {
            var parsed = JSON.parse(rawPhotos);
            if (Array.isArray(parsed)) {
                return parsed.filter(function (value) {
                    return typeof value === 'string' && value.length > 0;
                });
            }
        } catch (error) {
            return [];
        }

        return [];
    }

    function openDetailModal(buttonEl) {
        if (!detailModal) {
            return;
        }

        var requestId = buttonEl.getAttribute('data-request-id') || '-';
        var requestDescription = buttonEl.getAttribute('data-request-description') || 'Deskripsi belum tersedia.';
        var requestPhotos = parsePhotos(buttonEl.getAttribute('data-request-photos'));
        var requestMaterials = parseMaterials(buttonEl.getAttribute('data-request-materials'));

        activePhotos = requestPhotos.length > 0 ? requestPhotos : [placeholderImage];
        activePhotoIndex = 0;
        activeMaterials = requestMaterials;

        if (modalRequestIdEl) {
            modalRequestIdEl.textContent = requestId;
        }

        if (modalDescriptionEl) {
            modalDescriptionEl.textContent = requestDescription;
        }

        renderActivePhoto();
        renderMaterialListInModal();
        setModalOpenState(true);
    }

    if (detailModal) {
        document.addEventListener('click', function (event) {
            // allow clicking thumbnail wrap or thumbnail image to open the same detail modal
            var thumbWrap = event.target.closest('.rv-damage-photo-thumb-wrap') || event.target.closest('.rv-damage-photo-thumb');
            if (thumbWrap) {
                var requestCard = thumbWrap.closest('.rv-request-card');
                if (requestCard) {
                    var cardOpenBtn = requestCard.querySelector('.js-open-request-detail');
                    if (cardOpenBtn) {
                        event.preventDefault();
                        openDetailModal(cardOpenBtn);
                        return;
                    }
                }
            }

            var openButton = event.target.closest('.js-open-request-detail');
            if (openButton) {
                event.preventDefault();
                openDetailModal(openButton);
                return;
            }

            var closeButton = event.target.closest('.js-close-request-detail');
            if (closeButton) {
                event.preventDefault();
                setModalOpenState(false);
                return;
            }

            var navButton = event.target.closest('[data-gallery-nav]');
            if (navButton) {
                event.preventDefault();

                if (activePhotos.length <= 1) {
                    return;
                }

                var direction = navButton.getAttribute('data-gallery-nav');
                if (direction === 'next') {
                    activePhotoIndex = (activePhotoIndex + 1) % activePhotos.length;
                } else {
                    activePhotoIndex = (activePhotoIndex - 1 + activePhotos.length) % activePhotos.length;
                }

                renderActivePhoto();
                return;
            }

            var thumbButton = event.target.closest('[data-gallery-thumb-index]');
            if (thumbButton) {
                event.preventDefault();
                var nextIndex = Number(thumbButton.getAttribute('data-gallery-thumb-index'));

                if (!Number.isNaN(nextIndex)) {
                    activePhotoIndex = nextIndex;
                    renderActivePhoto();
                }
            }
        });

        document.addEventListener('keydown', function (event) {
            if (!detailModal.classList.contains('is-open')) {
                return;
            }

            if (event.key === 'Escape') {
                setModalOpenState(false);
            }
        });
    }

    var disabledDetailsList = document.querySelectorAll('.rv-review-details-disabled');

    disabledDetailsList.forEach(function (detailsEl) {
        detailsEl.open = false;

        var summaryEl = detailsEl.querySelector('.rv-review-summary');
        if (summaryEl) {
            summaryEl.setAttribute('tabindex', '-1');
            summaryEl.setAttribute('aria-disabled', 'true');
            summaryEl.addEventListener('click', function (event) {
                event.preventDefault();
            });
        }

        detailsEl.addEventListener('toggle', function () {
            if (detailsEl.open) {
                detailsEl.open = false;
            }
        });
    });
});
