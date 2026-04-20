document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('dashboard-review-modal');
    const requestMap = window.renovationRequestMap || {};

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
    const focusableSelector = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
    let lastTrigger = null;
    let selectedRequestId = null;

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
            feedbackInput.value = '';
        }

        if (costInput instanceof HTMLInputElement) {
            costInput.value = '';
        }

        if (takeRenovationButton instanceof HTMLButtonElement) {
            takeRenovationButton.dataset.requestId = request.id;
        }

        updateTakeRenovationButtonState();
    };

    const updateTakeRenovationButtonState = () => {
        if (!(takeRenovationButton instanceof HTMLButtonElement)) {
            return;
        }

        const feedbackValue = feedbackInput instanceof HTMLTextAreaElement ? feedbackInput.value.trim() : '';
        const costValue = costInput instanceof HTMLInputElement ? costInput.value.trim() : '';
        const isCostValid = Number(costValue) > 0;
        const hasRequestSelected = typeof selectedRequestId === 'string' && selectedRequestId.length > 0;
        const isFormComplete = feedbackValue.length > 0 && isCostValid && hasRequestSelected;

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
        takeRenovationButton.addEventListener('click', () => {
            const baseUrl = takeRenovationButton.dataset.trackingUrl;
            const requestId = takeRenovationButton.dataset.requestId;

            if (!baseUrl || !requestId) {
                return;
            }

            const targetUrl = new URL(baseUrl, window.location.origin);
            targetUrl.searchParams.set('request_id', requestId);
            window.location.href = targetUrl.toString();
        });
    }

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