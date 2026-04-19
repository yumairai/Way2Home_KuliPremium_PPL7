document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('dashboard-review-modal');

    if (!modal) {
        return;
    }

    const openButtons = document.querySelectorAll('[data-review-open]');
    const closeButtons = modal.querySelectorAll('[data-review-close]');
    const feedbackInput = modal.querySelector('.dashboard-review-feedback');
    const costInput = modal.querySelector('#dashboard-review-cost');
    const takeRenovationButton = modal.querySelector('.dashboard-review-actions .dashboard-review-action-btn-primary');
    const focusableSelector = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
    let lastTrigger = null;

    const updateTakeRenovationButtonState = () => {
        if (!(takeRenovationButton instanceof HTMLButtonElement)) {
            return;
        }

        const feedbackValue = feedbackInput instanceof HTMLTextAreaElement ? feedbackInput.value.trim() : '';
        const costValue = costInput instanceof HTMLInputElement ? costInput.value.trim() : '';
        const isCostValid = Number(costValue) > 0;
        const isFormComplete = feedbackValue.length > 0 && isCostValid;

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