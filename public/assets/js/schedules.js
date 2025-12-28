// ============================================================
// SCHEDULE FORM INTERACTIONS
// ============================================================
const userSubject = document.getElementById("userSubject");
const globalSubject = document.getElementById("globalSubject");
const customSubject = document.getElementById("customSubject");

function resetOthers(active) {
    if (active !== userSubject && userSubject) userSubject.value = "";
    if (active !== globalSubject && globalSubject) globalSubject.value = "";
    if (active !== customSubject && customSubject) customSubject.value = "";
}

if (userSubject) {
    userSubject.addEventListener("change", () => {
        if (userSubject.value) resetOthers(userSubject);
    });
}

if (globalSubject) {
    globalSubject.addEventListener("change", () => {
        if (globalSubject.value) resetOthers(globalSubject);
    });
}

if (customSubject) {
    customSubject.addEventListener("input", () => {
        if (customSubject.value.trim() !== "") resetOthers(customSubject);
    });
}

// ============================================================
// CHARACTER COUNTER
// ============================================================
const textarea = document.querySelector("textarea[name='description']");
const counter = document.querySelector(".char-counter");

if (textarea && counter) {
    textarea.addEventListener("input", () => {
        counter.textContent = textarea.value.length + " / 500";
    });
}

// ============================================================
// SIDEBAR TOGGLE
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
