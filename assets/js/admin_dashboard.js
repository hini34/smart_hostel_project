let attChart = null;

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) {
        el.innerText = value ?? 0;
    }
}

function updateStatus(isLive) {
    const liveStatus = document.getElementById("liveStatus");
    const lastUpdated = document.getElementById("lastUpdated");

    if (!liveStatus || !lastUpdated) return;

    if (isLive) {
        liveStatus.className = "badge text-bg-success live-badge";
        liveStatus.innerText = "LIVE";
        lastUpdated.innerText = "Last updated: " + new Date().toLocaleString();
    } else {
        liveStatus.className = "badge text-bg-danger live-badge";
        liveStatus.innerText = "OFFLINE";
        lastUpdated.innerText = "Last updated: Error";
    }
}

function renderAttendanceChart(chartData) {
    const canvas = document.getElementById("attendanceChart");
    if (!canvas) return;

    const ctx = canvas.getContext("2d");

    const labels = (chartData || []).map(item => item.attendance_date);
    const values = (chartData || []).map(item => item.present_count);

    if (attChart) {
        attChart.destroy();
    }

    attChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [{
                label: "Present Students",
                data: values,
                borderWidth: 3,
                tension: 0.35,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}

async function loadDashboard() {
    try {
        const response = await fetch("admin_dashboard_process.php?ts=" + Date.now(), {
            cache: "no-store"
        });

        if (!response.ok) {
            throw new Error("HTTP status " + response.status);
        }

        const text = await response.text();
        console.log("Raw response:", text);

        const data = JSON.parse(text);

        if (data.success === false) {
            throw new Error(data.error || "Server error");
        }

        setText("totalStudents", data.total_students);
        setText("totalRooms", data.total_rooms);
        setText("availableRooms", data.available_rooms);
        setText("leaveStudents", data.leave_students);
        setText("attendanceToday", data.attendance_today);
        setText("pendingComplaints", data.pending_complaints);
        setText("completedComplaints", data.completed_complaints);

        setText("todayRegs", data.today_regs);
        setText("todayAttendance", data.today_attendance);
        setText("todayComplaints", data.today_complaints);
        setText("todayResolved", data.today_resolved);
        setText("todayLeaves", data.today_leaves);

        renderAttendanceChart(data.attendance_chart || []);
        updateStatus(true);

    } catch (error) {
        console.error("Dashboard Error:", error);
        updateStatus(false);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    loadDashboard();
    setInterval(loadDashboard, 10000);
});