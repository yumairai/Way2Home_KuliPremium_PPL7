(function () {
    if (window.W2HDialog) {
        return;
    }

    const dialog = document.querySelector('[data-w2h-dialog]');

    const createFallback = () => ({
        alert: (message) => Promise.resolve(window.alert(String(message ?? ''))),
        success: (message) => Promise.resolve(window.alert(String(message ?? ''))),
        error: (message) => Promise.resolve(window.alert(String(message ?? ''))),
        info: (message) => Promise.resolve(window.alert(String(message ?? ''))),
        confirm: (message) => Promise.resolve(window.confirm(String(message ?? ''))),
        show: ({ message, showCancel } = {}) =>
            showCancel
                ? Promise.resolve(window.confirm(String(message ?? '')))
                : Promise.resolve(window.alert(String(message ?? ''))),
    });

    if (!dialog) {
        window.W2HDialog = createFallback();
        window.W2HAlert = window.W2HDialog;
        window.W2HConfirm = window.W2HDialog;
        return;
    }

    const titleEl = dialog.querySelector('[data-w2h-dialog-title]');
    const messageEl = dialog.querySelector('[data-w2h-dialog-message]');
    const eyebrowEl = dialog.querySelector('[data-w2h-dialog-eyebrow]');
    const iconEl = dialog.querySelector('[data-w2h-dialog-icon-name]');
    const confirmButton = dialog.querySelector('[data-w2h-dialog-confirm]');
    const cancelButton = dialog.querySelector('[data-w2h-dialog-cancel]');
    const closeButtons = dialog.querySelectorAll('[data-w2h-dialog-close]');

    const variantConfig = {
        info: { icon: 'info', title: 'Informasi', confirmText: 'OK' },
        success: { icon: 'check_circle', title: 'Berhasil', confirmText: 'OK' },
        warning: { icon: 'warning', title: 'Konfirmasi', confirmText: 'Lanjutkan', cancelText: 'Batal' },
        error: { icon: 'error', title: 'Terjadi Kesalahan', confirmText: 'OK' },
    };

    const state = {
        resolve: null,
        lastFocused: null,
        mode: 'alert',
    };

    const restoreFocus = () => {
        if (state.lastFocused instanceof HTMLElement) {
            state.lastFocused.focus();
        }
        state.lastFocused = null;
    };

    const closeDialog = (result) => {
        if (dialog.hidden) {
            return;
        }

        dialog.classList.remove('is-open');
        dialog.setAttribute('aria-hidden', 'true');
        dialog.hidden = true;
        document.body.classList.remove('w2h-dialog-open');

        const resolve = state.resolve;
        state.resolve = null;

        if (typeof resolve === 'function') {
            resolve(result);
        }

        restoreFocus();
    };

    const openDialog = ({
        title,
        message,
        variant = 'info',
        confirmText,
        cancelText,
        showCancel = false,
    } = {}) => {
        const config = variantConfig[variant] || variantConfig.info;

        if (state.resolve) {
            closeDialog(false);
        }

        state.mode = showCancel ? 'confirm' : 'alert';
        state.lastFocused = document.activeElement instanceof HTMLElement ? document.activeElement : null;

        dialog.dataset.variant = variant;
        dialog.dataset.dialogMode = state.mode;
        dialog.hidden = false;
        dialog.setAttribute('aria-hidden', 'false');
        document.body.classList.add('w2h-dialog-open');

        if (titleEl) {
            titleEl.textContent = title || config.title;
        }

        if (messageEl) {
            messageEl.textContent = String(message ?? '');
        }

        if (iconEl) {
            iconEl.textContent = config.icon;
        }

        if (confirmButton) {
            confirmButton.textContent = confirmText || config.confirmText;
        }

        if (cancelButton) {
            cancelButton.textContent = cancelText || config.cancelText || 'Batal';
            cancelButton.hidden = !showCancel;
        }

        requestAnimationFrame(() => {
            dialog.classList.add('is-open');
            (confirmButton || closeButtons[0] || dialog).focus?.();
        });

        return new Promise((resolve) => {
            state.resolve = resolve;
        });
    };

    const dialogApi = {
        show: openDialog,
        alert: (message, options = {}) => openDialog({ ...options, message, variant: options.variant || 'info', showCancel: false, confirmText: options.confirmText || 'OK' }),
        success: (message, options = {}) => openDialog({ ...options, message, variant: 'success', showCancel: false, confirmText: options.confirmText || 'OK' }),
        error: (message, options = {}) => openDialog({ ...options, message, variant: 'error', showCancel: false, confirmText: options.confirmText || 'OK' }),
        info: (message, options = {}) => openDialog({ ...options, message, variant: 'info', showCancel: false, confirmText: options.confirmText || 'OK' }),
        confirm: (message, options = {}) => openDialog({ ...options, message, variant: options.variant || 'warning', showCancel: true, confirmText: options.confirmText || 'Lanjutkan', cancelText: options.cancelText || 'Batal' }),
        close: () => closeDialog(false),
    };

    const showFlashQueue = async () => {
        const queue = Array.isArray(window.W2HFlashMessages) ? [...window.W2HFlashMessages] : [];

        if (!queue.length) {
            return;
        }

        window.W2HFlashMessages = [];

        for (const item of queue) {
            if (!item || !item.message) {
                continue;
            }

            const variant = item.variant || 'info';
            const options = {
                title: item.title,
                confirmText: item.confirmText,
                cancelText: item.cancelText,
            };

            if (variant === 'success') {
                await dialogApi.success(item.message, options);
                continue;
            }

            if (variant === 'error') {
                await dialogApi.error(item.message, options);
                continue;
            }

            if (variant === 'warning') {
                await dialogApi.alert(item.message, { ...options, variant: 'warning' });
                continue;
            }

            await dialogApi.info(item.message, options);
        }
    };

    closeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            closeDialog(state.mode === 'alert');
        });
    });

    if (confirmButton) {
        confirmButton.addEventListener('click', () => {
            closeDialog(true);
        });
    }

    if (cancelButton) {
        cancelButton.addEventListener('click', () => {
            closeDialog(false);
        });
    }

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog || event.target.hasAttribute('data-w2h-dialog-close')) {
            closeDialog(state.mode === 'alert');
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !dialog.hidden) {
            closeDialog(state.mode === 'alert');
        }
    });

    const startFlashQueue = () => {
        showFlashQueue().catch(() => {
            window.W2HFlashMessages = [];
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startFlashQueue, { once: true });
    } else {
        startFlashQueue();
    }

    window.W2HDialog = dialogApi;
    window.W2HAlert = dialogApi;
    window.W2HConfirm = dialogApi;
})();