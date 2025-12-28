// ============================================================
// ALERT AUTO DISMISS
// ============================================================
setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) alert.style.display = 'none';
}, 6000);

// ============================================================
// SIDEBAR TOGGLE
// ============================================================
document.getElementById("toggleSidebar")
    ?.addEventListener("click", () => {
        document.querySelector(".dashboard").classList.toggle("collapsed");
    });

// ============================================================
// CHARACTER COUNTER
// ============================================================
const textarea = document.querySelector('textarea[name="note"]');
const counter = document.querySelector('.char-counter');
if (textarea && counter) {
    textarea.addEventListener('input', () => {
        counter.textContent = textarea.value.length + " / 500";
    });
}
