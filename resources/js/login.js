document.addEventListener("DOMContentLoaded", () => {
    const errorCard = document.getElementById("error-card");
    const hiddenErrorCardButton = document.getElementById("hidden-error-card");

    if (hiddenErrorCardButton && errorCard) {
        hiddenErrorCardButton.addEventListener("click", () => {
            errorCard.classList.add("hide");

            errorCard.addEventListener(
                "transitionend",
                () => {
                    errorCard.style.display = "none";
                },
                { once: true }
            );
        });
    }
});
