function assignDataLabels() {
    const table = document.querySelector(".queries__table");
    if (!table) return;
    const headers = Array.from(table.querySelectorAll("thead th"));
    const rows = table.querySelectorAll("tbody tr");
    rows.forEach((row) => {
        Array.from(row.children).forEach((cell, i) => {
            if (headers[i]) {
                cell.setAttribute("data-label", headers[i].textContent.trim());
            }
        });
    });
    console.log("Data-labels asignados");
}

document.addEventListener("DOMContentLoaded", assignDataLabels);

document.addEventListener("livewire:update", () => {
    assignDataLabels();
});
