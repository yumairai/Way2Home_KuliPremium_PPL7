document.addEventListener('DOMContentLoaded', function () {
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
                applyStateToCard(requestCard, nextState);
                if (nextState === 'on-progress') {
                    alert('Status request renovasi berhasil diubah menjadi Renovasi Aktif.');
                } else if (nextState === 'completed') {
                    alert('Status request renovasi berhasil diubah menjadi Request Renovasi Selesai.');
                }
            }

            return;
        }

        var alertButton = event.target.closest('.js-alert-btn');

        if (!alertButton) {
            return;
        }

        event.preventDefault();

        var message = alertButton.getAttribute('data-alert-message') || 'Fitur ini akan segera tersedia.';
        alert(message);
    });

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
