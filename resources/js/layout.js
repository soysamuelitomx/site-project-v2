document.addEventListener("DOMContentLoaded", () => {
    window.addEventListener("DOMContentLoaded", () => {
        document.body.classList.add("loaded");
    });

    const nav = document.querySelector(".sidebar__nav");
    const checkbox = document.getElementById("show-sidebar");

    checkbox.addEventListener("change", () => {
        nav.classList.toggle("sidebar__nav--visible", checkbox.checked);
    });

    const userButton = document.getElementById("user-button");

    const logoutForm = document.getElementById("user-logout-form");

    userButton.addEventListener("click", () => {
        logoutForm.classList.toggle("header__logout-form--visible");
    });
    window.addEventListener("load", () => {
        document.body.style.visibility = "visible";
        document.body.style.opacity = "1";
    });
});
