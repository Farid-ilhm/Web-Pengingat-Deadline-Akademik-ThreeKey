document.addEventListener('DOMContentLoaded', function () {

    /* ================= TANDAI DIBACA ================= */
    document.querySelectorAll('.btn-read').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const id = this.dataset.id;
            const row = this.closest('.notif-row');

            fetch('notif_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=read&id=${id}`
            })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) return;

                    row.classList.remove('unread');
                    row.classList.add('read');
                    this.remove();

                    updateBadge(data.unread);
                });
        });
    });

    /* ================= HAPUS ================= */
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const id = this.dataset.id;
            const row = this.closest('.notif-row');

            if (!confirm('Hapus notifikasi ini?')) return;

            fetch('notif_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete&id=${id}`
            })
                .then(() => row.remove());
        });
    });

    /* ================= UPDATE BADGE ================= */
    function updateBadge(count) {
        const top = document.getElementById('notifBadgeTop');
        const side = document.getElementById('notifBadgeSide');

        if (count > 0) {
            if (top) top.textContent = count;
            if (side) side.textContent = count;
        } else {
            if (top) top.remove();
            if (side) side.remove();
        }
    }

    // Sidebar Toggle Logic
    const toggleBtn = document.querySelector('.toggle-btn');
    const dashboard = document.querySelector('.dashboard');

    if (toggleBtn && dashboard) {
        toggleBtn.addEventListener('click', function () {
            dashboard.classList.toggle('collapsed');
        });
    }

});
