// Auto-hide alerts
setTimeout(() => {
    document.querySelectorAll('.auto-hide').forEach(el => {
        el.style.transition = "opacity .5s";
        el.style.opacity = "0";
        setTimeout(() => el.remove(), 500);
    });
}, 3000);
