function loadStudentDashboard() {

    const errorBox = document.getElementById('dashboardError');

    fetch('student_dashboard_process.php')
        .then(res => res.json())
        .then(data => {

            let hasAnyData = false;

            // ================= ROOM =================
           document.getElementById('stat-room').innerHTML =
                (data.room_no && data.room_no !== 'Not assigned')
                    ? data.room_no
                    : '<span class="text-danger mb-0">Room not assigned</span>';

            document.getElementById('stat-bed').textContent =
                'Floor: ' + (data.floor_no || 'Not available');

            
            if (!data.complaints_total && !data.complaints_pending) {

                document.getElementById('stat-complaints-pending').innerHTML =
                    '<span class="text-danger mb-0">No complaint has been raised</span>';

                document.getElementById('stat-complaints-total').style.display = "none";

            } else {
                document.getElementById('stat-complaints-pending').textContent =
                    data.complaints_pending ?? 0;

                document.getElementById('stat-complaints-total').style.display = "block";
                document.getElementById('stat-complaints-total').textContent =
                    'Total: ' + (data.complaints_total ?? 0);
            }

           const status = data.leave_status;
            const dates = data.leave_dates;

            // ================= LEAVE =================
            if (!status) {
                document.getElementById('stat-leave-status').innerHTML =
                    '<span class="text-danger">No leave has been applied</span>';

                document.getElementById('stat-leave-dates').style.display = "none";

            } else {

                // ✔ STATUS EXISTS (Approved / Pending / Rejected)

                let statusHTML = status;

                if (status === "Pending") {
                    statusHTML = '<span class="text-warning mb-0">Pending</span>';
                } 
                else if (status === "Approved") {
                    statusHTML = '<span class="text-success mb-0">Approved</span>';
                } 
                else if (status === "Rejected") {
                    statusHTML = '<span class="text-danger mb-0">Rejected</span>';
                }

                document.getElementById('stat-leave-status').innerHTML = statusHTML;

                // ================= DATES =================
                if (dates) {
                    document.getElementById('stat-leave-dates').style.display = "block";
                    document.getElementById('stat-leave-dates').textContent = dates;
                } else {
                    document.getElementById('stat-leave-dates').style.display = "none";
                }
            }


            // ================= NOTICE =================
            if (data.notice_title && data.notice_title !== 'No recent notice') {
                hasAnyData = true;

                document.getElementById('notice-title').textContent =
                    data.notice_title;

                document.getElementById('notice-date').textContent =
                    data.notice_date || '--';

                document.getElementById('notice-text').textContent =
                    data.notice_text || '';
            } else {
                document.getElementById('notice-title').innerHTML =
                    '<span class="text-danger">No Notice Available</span>';

                document.getElementById('notice-text').innerHTML =
                    '<span class="text-danger">No notice data found.</span>';
            }

            // ================= ERROR BOX LOGIC =================
            if (!hasAnyData) {
                errorBox.classList.remove('d-none');   // SHOW
            } else {
                errorBox.classList.add('d-none');      // HIDE
            }

            document.getElementById('lastUpdated').textContent =
                'Last updated: ' + new Date().toLocaleTimeString();

        })
        .catch(error => {
            console.error(error);

            errorBox.classList.remove('d-none');
            errorBox.innerHTML =
                '<i class="fa-solid fa-triangle-exclamation me-2"></i> Unable to load dashboard data or data is not Available.';
        });
    }
  function startClock() {
    setInterval(() => {
        document.getElementById('lastUpdated').textContent =
            'Last updated: ' + new Date().toLocaleTimeString();
    }, 1000);
  }



loadStudentDashboard();
setInterval(loadStudentDashboard, 10000);
startClock();