document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('dashboard-review-modal');

    if (!modal) {
        return;
    }

    const openButtons = document.querySelectorAll('[data-review-open]');
    const closeButtons = modal.querySelectorAll('[data-review-close]');
    const focusableSelector = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
    let lastTrigger = null;

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