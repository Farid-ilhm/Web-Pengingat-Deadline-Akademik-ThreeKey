// ============================================================
// NOTIFICATION DROPDOWN LOGIC
// ============================================================
const notifToggle = document.getElementById('notifToggle');
const notifDropdown = document.getElementById('notifDropdown');

if (notifToggle && notifDropdown) {
    notifToggle.addEventListener('click', function (e) {
        e.stopPropagation();
        notifDropdown.style.display =
            notifDropdown.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', function () {
        notifDropdown.style.display = 'none';
    });
}

// ============================================================
// SIDEBAR TOGGLE LOGIC
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.querySelector('.toggle-btn');
    const dashboard = document.querySelector('.dashboard');

    if (toggleBtn && dashboard) {
        toggleBtn.addEventListener('click', function () {
            dashboard.classList.toggle('collapsed');
        });
    }
});

// ============================================================
// CHART INITIALIZATION
// ============================================================
// Note: chartWeekData, chartWeekLabels, chartSubjectData, chartSubjectLabels must be defined in the PHP view before loading this script.

if (typeof Chart !== 'undefined') {
    const ctxWeek = document.getElementById('chartWeek');
    if (ctxWeek && typeof chartWeekLabels !== 'undefined' && typeof chartWeekData !== 'undefined') {
        new Chart(ctxWeek, {
            type: 'line',
            data: {
                labels: chartWeekLabels,
                datasets: [{
                    data: chartWeekData,
                    fill: true,
                    tension: .4,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,.15)',
                    pointRadius: 5
                }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    }

    const ctxSubject = document.getElementById('chartSubject');
    if (ctxSubject && typeof chartSubjectLabels !== 'undefined' && typeof chartSubjectData !== 'undefined') {
        new Chart(ctxSubject, {
            type: 'bar',
            data: {
                labels: chartSubjectLabels,
                datasets: [{
                    data: chartSubjectData,
                    backgroundColor: ['#60a5fa', '#34d399', '#fbbf24', '#f87171', '#a78bfa'],
                    borderRadius: 10
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }
}
