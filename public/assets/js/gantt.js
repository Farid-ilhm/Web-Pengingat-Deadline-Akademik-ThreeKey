// Gantt Chart Logic
// Note: ganttDatasets, ganttMinDate, ganttMaxDate must be defined in the PHP view

if (typeof Chart !== 'undefined' && document.getElementById('ganttChart')) {
    if (typeof ganttDatasets !== 'undefined') {
        new Chart(document.getElementById('ganttChart'), {
            type: 'bar',
            data: {
                datasets: [{
                    data: ganttDatasets,
                    backgroundColor: ctx => ctx.raw.backgroundColor,
                    barThickness: 18,
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        type: 'time',
                        min: typeof ganttMinDate !== 'undefined' ? ganttMinDate : undefined,
                        max: typeof ganttMaxDate !== 'undefined' ? ganttMaxDate : undefined,
                        time: {
                            unit: 'day',
                            displayFormats: { day: 'dd MMM' },
                            tooltipFormat: 'dd MMM yyyy'
                        },
                        title: {
                            display: true,
                            text: 'Waktu'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Jadwal'
                        }
                    }
                }
            }
        });
    }
}

// Sidebar Toggle Logic
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.querySelector('.toggle-btn');
    const dashboard = document.querySelector('.dashboard');

    if (toggleBtn && dashboard) {
        toggleBtn.addEventListener('click', function () {
            dashboard.classList.toggle('collapsed');
        });
    }
});
