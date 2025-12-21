// Alert Auto-dismiss
setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) alert.style.display = 'none';
}, 6000);

// Sidebar Toggle
document.getElementById("toggleSidebar")
    ?.addEventListener("click", () => {
        document.querySelector(".dashboard").classList.toggle("collapsed");
    });

// Character Counter
const textarea = document.querySelector('textarea[name="note"]');
const counter = document.querySelector('.char-counter');
if (textarea && counter) {
    textarea.addEventListener('input', () => {
        counter.textContent = textarea.value.length + " / 500";
    });
}
