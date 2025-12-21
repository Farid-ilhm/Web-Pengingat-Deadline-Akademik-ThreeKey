// Sidebar Toggle Logic for Edit Profile Page
document.getElementById("toggleSidebar")
    ?.addEventListener("click", () => {
        document.querySelector(".dashboard")
            .classList.toggle("collapsed");
    });
