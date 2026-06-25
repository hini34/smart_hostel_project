document.addEventListener("DOMContentLoaded", function () {
    const searchBtn = document.getElementById("searchBtn");
    const enrollmentInput = document.getElementById("enrollmentInput");
    const resultArea = document.getElementById("resultArea");

    function getStatusClass(status) {
        switch (status) {
            case "Approved":
                return "approved";
            case "Pending":
                return "pending";
            case "Rejected":
                return "rejected";
            default:
                return "default";
        }
    }

    function getGateClass(status) {
        switch (status) {
            case "Allowed":
                return "gate-allowed";
            case "Expired":
                return "gate-expired";
            case "Pending Approval":
                return "gate-pending";
            case "Rejected":
                return "gate-rejected";
            default:
                return "gate-default";
        }
    }

    function renderData(data) {
        const student = data.student;
        const leave = data.leave;

        resultArea.innerHTML = `
            <div class="verification-grid">
                <div class="info-card">
                    <div class="card-title">Student Information</div>
                    <div class="info-list">
                        <div><strong>Name:</strong> ${student.name || "N/A"}</div>
                        <div><strong>Enrollment:</strong> ${student.enrollment || "N/A"}</div>
                        <div><strong>Course:</strong> ${student.course || "N/A"}</div>
                        <div><strong>Department:</strong> ${student.department || "N/A"}</div>
                        <div><strong>Semester:</strong> ${student.semester || "N/A"}</div>
                        <div><strong>Room No:</strong> ${student.room_no || "N/A"}</div>
                        <div><strong>Block:</strong> ${student.block_name || "N/A"}</div>
                        <div><strong>Floor:</strong> ${student.floor_no || "N/A"}</div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="card-title">Leave Details</div>
                    <div class="info-list">
                        <div><strong>Leave Type:</strong> ${leave.leave_type || "N/A"}</div>
                        <div><strong>From Date:</strong> ${leave.from_date || "N/A"}</div>
                        <div><strong>To Date:</strong> ${leave.to_date || "N/A"}</div>
                        <div><strong>Reason:</strong> ${leave.reason || "N/A"}</div>
                        <div><strong>Going Address:</strong> ${leave.going_address || "N/A"}</div>
                        <div><strong>Contact No:</strong> ${leave.contact_number || "N/A"}</div>
                        <div><strong>Emergency Contact:</strong> ${leave.emergency_contact || "N/A"}</div>
                        <div><strong>Applied On:</strong> ${leave.applied_on || "N/A"}</div>
                        <div><strong>Approved By:</strong> ${leave.approved_by || "N/A"}</div>
                        <div><strong>Approved At:</strong> ${leave.approved_at || "N/A"}</div>
                    </div>
                </div>
            </div>

            <div class="status-card">
                <div class="status-row">
                    <div>
                        <h3>Leave Status</h3>
                        <span class="status-badge ${getStatusClass(leave.status)}">${leave.status}</span>
                    </div>
                    <div>
                        <h3>Gate Pass Status</h3>
                        <span class="gate-badge ${getGateClass(leave.gate_pass_status)}">${leave.gate_pass_status}</span>
                    </div>
                </div>

                <div class="remark-box">
                    <strong>Admin Remark:</strong><br>
                    ${leave.admin_remark ? leave.admin_remark : "No admin remark available."}
                </div>
            </div>
        `;
    }

    function renderError(message) {
        resultArea.innerHTML = `
            <div class="empty-state error-state">
                <h4>Record Not Found</h4>
                <p>${message}</p>
            </div>
        `;
    }

    function verifyLeave() {
        const enrollment = enrollmentInput.value.trim();

        if (!enrollment) {
            renderError("Please enter enrollment number first.");
            return;
        }

        resultArea.innerHTML = `
            <div class="empty-state">
                <h4>Checking...</h4>
                <p>Fetching leave verification data.</p>
            </div>
        `;

        fetch(`leave_verification_data.php?enrollment=${encodeURIComponent(enrollment)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderData(data);
                } else {
                    renderError(data.message || "No record found.");
                }
            })
            .catch(error => {
                console.error(error);
                renderError("Something went wrong while verifying leave status.");
            });
    }

    searchBtn.addEventListener("click", verifyLeave);

    enrollmentInput.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            verifyLeave();
        }
    });
});