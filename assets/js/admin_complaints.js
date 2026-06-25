document.addEventListener("DOMContentLoaded", function () {
    const rows = document.querySelectorAll(".complaint-row");

    rows.forEach((row) => {
        row.addEventListener("mouseenter", () => {
            row.style.transform = "scale(1.002)";
        });

        row.addEventListener("mouseleave", () => {
            row.style.transform = "scale(1)";
        });
    });
});