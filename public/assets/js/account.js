// Sidebar Toggle Logic for Account Page
// Note: This logic seems redundant with layout.js but is present in the original file.
// Ideally, layout.js should handle all sidebar toggles if they share the same ID/class.

document.getElementById("toggleSidebar")
    ?.addEventListener("click", () => {
        document.querySelector(".dashboard").classList.toggle("collapsed");
    });
