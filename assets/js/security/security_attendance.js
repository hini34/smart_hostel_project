let searchTimeout = null;

function showAlert(message, type = "success") {
    const alertBox = document.getElementById("alertBox");
    alertBox.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
}

function loadAttendanceData(search = "") {
    fetch("attendance_data.php?search=" + encodeURIComponent(search))
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                showAlert(data.message || "Failed to load attendance data", "danger");
                return;
            }

            document.getElementById("totalStudents").textContent = data.counts.total_students ?? 0;
            document.getElementById("presentStudents").textContent = data.counts.present_students ?? 0;
            document.getElementById("absentStudents").textContent = data.counts.absent_students ?? 0;
            document.getElementById("lastUpdated").textContent = data.last_updated ?? "--";

            renderAttendanceTable(data.students || []);
        })
        .catch(error => {
            console.error("Attendance load error:", error);
            showAlert("Something went wrong while loading attendance data", "danger");
        });
}

function renderAttendanceTable(students) {
    const tbody = document.getElementById("attendanceTableBody");

    if (!students.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">No students found</td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = students.map(student => {
        const status = (student.attendance_status || "Absent").toLowerCase();
        const badgeClass = status === "present" ? "success" : "danger";

        return `
            <tr>
                <td>${student.sr_no}</td>
               <td>
                    <img 
                        src="../../${student.photo}" 
                        alt="Student" 
                        class="student-photo"
                        onerror="this.src='../../assets/images/default-user.png'">
                </td>
                <td><div class="student-name">${student.fullname}</div></td>
                <td>${student.enrollment}</td>
                <td>${student.room_no ?? '-'}</td>
                <td>
                    <span class="badge text-bg-${badgeClass} status-badge">
                        ${student.attendance_status}
                    </span>
                </td>
                <td>
                    <div class="action-btn-group">
                        <button class="btn btn-sm btn-success" onclick="saveAttendance(${student.id}, 'Present')">
                            <i class="fa-solid fa-check me-1"></i>Present
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="saveAttendance(${student.id}, 'Absent')">
                            <i class="fa-solid fa-xmark me-1"></i>Absent
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join("");
}

function saveAttendance(studentId, status) {
    const formData = new FormData();
    formData.append("student_id", studentId);
    formData.append("status", status);
    formData.append("action", "single");

    fetch("attendance_save.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, "success");
            const currentSearch = document.getElementById("searchInput").value.trim();
            loadAttendanceData(currentSearch);
        } else {
            showAlert(data.message || "Failed to save attendance", "danger");
        }
    })
    .catch(error => {
        console.error("Attendance save error:", error);
        showAlert("Something went wrong while saving attendance", "danger");
    });
}

function markAllPresent() {
    const formData = new FormData();
    formData.append("action", "mark_all_present");

    fetch("attendance_save.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, "success");
            const currentSearch = document.getElementById("searchInput").value.trim();
            loadAttendanceData(currentSearch);
        } else {
            showAlert(data.message || "Failed to mark all present", "danger");
        }
    })
    .catch(error => {
        console.error("Mark all present error:", error);
        showAlert("Something went wrong while updating attendance", "danger");
    });
}

document.addEventListener("DOMContentLoaded", function () {
    loadAttendanceData();

    const searchInput = document.getElementById("searchInput");
    const markAllPresentBtn = document.getElementById("markAllPresentBtn");

    if (searchInput) {
        searchInput.addEventListener("input", function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadAttendanceData(this.value.trim());
            }, 300);
        });
    }

    if (markAllPresentBtn) {
        markAllPresentBtn.addEventListener("click", markAllPresent);
    }

    setInterval(() => {
        const currentSearch = document.getElementById("searchInput").value.trim();
        loadAttendanceData(currentSearch);
    }, 5000);
});