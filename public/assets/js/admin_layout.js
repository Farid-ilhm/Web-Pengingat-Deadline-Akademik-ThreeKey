document.addEventListener('DOMContentLoaded', function () {
    const logoutBtn = document.getElementById('adminLogoutBtn');
    const modal = document.getElementById('adminLogoutModal');
    const cancelBtn = document.getElementById('cancelAdminLogout');

    if (!logoutBtn || !modal) return;

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
});
