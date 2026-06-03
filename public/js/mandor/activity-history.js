document.addEventListener('DOMContentLoaded', function () {
    const activityList = document.getElementById('dashboard-activity-list');
    const activityEmpty = document.getElementById('dashboard-activity-empty');
    const expandBtn = document.getElementById('dashboard-activity-expand-btn');
    const summaryWrapper = document.querySelector('.dashboard-summary-wrapper');
    const statsColumn = document.querySelector('.dashboard-stats');
    const historyPanel = document.querySelector('.dashboard-activity-history');

    if (!activityList || !expandBtn) return;

    const syncHistoryHeight = () => {
        if (!summaryWrapper || !statsColumn || !historyPanel) {
            return;
        }

        const isMobile = window.matchMedia('(max-width: 767px)').matches;

        if (isMobile) {
            historyPanel.style.height = '';
            return;
        }

        historyPanel.style.height = `${statsColumn.offsetHeight}px`;
    };

    const hasItems = activityList.querySelectorAll('.dashboard-activity-item').length > 0;

    if (!hasItems) {
        activityList.style.display = 'none';
        if (activityEmpty) {
            activityEmpty.style.display = 'block';
        }
        expandBtn.style.display = 'none';
        return;
    }

    syncHistoryHeight();
    window.addEventListener('resize', syncHistoryHeight);

    window.addEventListener('load', syncHistoryHeight);

    expandBtn.addEventListener('click', function () {
        const isExpanded = activityList.classList.contains('dashboard-activity-list-expanded');

        if (isExpanded) {
            activityList.classList.remove('dashboard-activity-list-expanded');
            activityList.classList.add('dashboard-activity-list-collapsed');
            expandBtn.textContent = 'Lihat keseluruhan';
        } else {
            activityList.classList.remove('dashboard-activity-list-collapsed');
            activityList.classList.add('dashboard-activity-list-expanded');
            expandBtn.textContent = 'Sembunyikan';
        }

        syncHistoryHeight();
    });
});
