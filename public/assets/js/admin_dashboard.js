// ============================================================
// ADMIN DASHBOARD CHARTS
// ============================================================

if (typeof Chart !== 'undefined') {
    // ============================================================
    // TOTAL USER
    // ============================================================
    if (document.getElementById('chartTotalUser') && typeof adminChartTotalUsers !== 'undefined') {
        new Chart(document.getElementById('chartTotalUser'), {
            type: 'line',
            data: {
                labels: adminChartLabels,
                datasets: [{
                    label: 'Jumlah User Terdaftar',
                    data: adminChartTotalUsers,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    borderColor: '#0b2a45',
                    backgroundColor: 'rgba(11,42,69,0.15)',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // ============================================================
    // USER BARU
    // ============================================================
    if (document.getElementById('chartNewUser') && typeof adminChartNewUsers !== 'undefined') {
        new Chart(document.getElementById('chartNewUser'), {
            type: 'bar',
            data: {
                labels: adminChartLabels,
                datasets: [{
                    label: 'User Baru',
                    data: adminChartNewUsers,
                    backgroundColor: '#d6a73c',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
}
