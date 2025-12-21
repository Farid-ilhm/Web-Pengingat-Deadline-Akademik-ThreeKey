// Character Counter for Description
const textarea = document.querySelector("textarea[name='description']");
const counter = document.querySelector(".char-counter");

if (textarea && counter) {
    textarea.addEventListener("input", () => {
        counter.textContent = textarea.value.length + " / 500";
    });
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
