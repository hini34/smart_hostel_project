function setText(id, value) {
    const el = document.getElementById(id);
    if (el) {
        el.textContent = value;
    }
}

function loadDashboardData() {
    fetch("security_dashboard_data.php")
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                setText("serverStatus", data.message || "Failed");
                return;
            }

            setText("totalStudents", data.total_students ?? 0);
            setText("presentStudents", data.present_students ?? 0);
            setText("absentStudents", data.absent_students ?? 0);
            setText("visitorsToday", data.visitors_today ?? 0);
            setText("pendingApprovals", data.pending_approvals ?? 0);
            setText("activeLeaves", data.active_leaves ?? 0);
            setText("lastUpdated", data.last_updated ?? "--");
            setText("serverStatus", "Connected");
        })
        .catch(error => {
            console.error("Fetch error:", error);
            setText("serverStatus", "Connection error");
        });
}

document.addEventListener("DOMContentLoaded", function () {
    loadDashboardData();
    setInterval(loadDashboardData, 5000);
});