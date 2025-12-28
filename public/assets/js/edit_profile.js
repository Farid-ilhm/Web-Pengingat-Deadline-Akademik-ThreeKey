// ============================================================
// SIDEBAR TOGGLE LOGIC FOR EDIT PROFILE PAGE
// ============================================================
document.getElementById("toggleSidebar")
    ?.addEventListener("click", () => {
        document.querySelector(".dashboard")
            .classList.toggle("collapsed");
    });
