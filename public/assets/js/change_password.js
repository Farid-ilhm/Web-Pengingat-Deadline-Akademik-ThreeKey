// Password Visibility Toggle
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.target);
        const img = button.querySelector('img');

        if (input.type === 'password') {
            input.type = 'text';
            img.src = "../assets/img/eye-open.png";
        } else {
            input.type = 'password';
            img.src = "../assets/img/eye-close.png";
        }
    });
});

// Sidebar Toggle
document.getElementById("toggleSidebar")
    ?.addEventListener("click", () => {
        document.querySelector(".dashboard").classList.toggle("collapsed");
    });

// Auto-hide alerts
setTimeout(() => {
    document.querySelectorAll('.auto-hide').forEach(el => {
        el.style.transition = "opacity 0.5s ease";
        el.style.opacity = "0";
        setTimeout(() => el.remove(), 500);
    });
}, 2500);
