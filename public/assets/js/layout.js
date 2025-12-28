document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // NOTIFICATION DROPDOWN
    // ============================================================
    const toggle = document.getElementById('notifToggle');
    const dropdown = document.getElementById('notifDropdown');

    if (toggle && dropdown) {
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });

        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target) && !toggle.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    }

    // ============================================================
    // LOGOUT MODAL
    // ============================================================
    const logoutBtn = document.getElementById('logoutBtn');
    const modal = document.getElementById('logoutModal');
    const cancelBtn = document.getElementById('cancelLogout');

    if (logoutBtn && modal) {
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();
            modal.classList.add('show');
        });

        cancelBtn.addEventListener('click', function () {
            modal.classList.remove('show');
        });

        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.classList.remove('show');
            }
        });
    }

});
