// ============================================================
// CHARACTER COUNTER FOR NOTE
// ============================================================
const textarea = document.querySelector("textarea[name='note']");
const counter = document.querySelector(".char-counter");

if (textarea && counter) {
    textarea.addEventListener("input", () => {
        counter.textContent = textarea.value.length + " / 500";
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
