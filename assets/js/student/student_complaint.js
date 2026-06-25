document.addEventListener("DOMContentLoaded", function () {
    const hash = window.location.hash;
    if (hash === "#status") {
        const statusTab = document.querySelector('#status-tab');
        if (statusTab) {
            bootstrap.Tab.getOrCreateInstance(statusTab).show();
        }
    }
});