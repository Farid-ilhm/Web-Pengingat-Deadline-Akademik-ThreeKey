document.addEventListener("DOMContentLoaded", () => {

  document.querySelectorAll(".password-wrapper").forEach(wrapper => {

    const input = wrapper.querySelector("input");
    const icon  = wrapper.querySelector(".eye-icon");

    if (!input || !icon) return;

    icon.addEventListener("click", () => {
      const isHidden = input.type === "password";

      input.type = isHidden ? "text" : "password";
      icon.src = isHidden
        ? "../assets/img/eye-open.png"
        : "../assets/img/eye-close.png";
    });

  });

});
