// ============================================================
// SIDEBAR TOGGLE LOGIC FOR ACCOUNT PAGE
// ============================================================

document.getElementById("toggleSidebar")
    ?.addEventListener("click", () => {
        document.querySelector(".dashboard").classList.toggle("collapsed");
    });
