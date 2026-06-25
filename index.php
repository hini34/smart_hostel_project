<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Hostel Management</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!-- Navbar -->
     <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-building"></i> Hostel Management</a>
            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
             <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navmenu">
                <ul class="navbar-nav ms-auto">
                  <li class="nav-item">
                    <a class="nav-link" href="about.php">
                        <i class="fas fa-info-circle"></i> About
                    </a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link" href="#features"><i class="fa-sharp fa-solid fa-star"></i> Features</a>
                  </li>
                  <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="loginDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>

                     <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="/smart_hostel/auth/admins/admin_login.php">
                                <i class="fas fa-user-shield"></i> Admin Login
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/smart_hostel/auth/students/student_login.php">
                                <!-- <a class="dropdown-item" href="/student_login.php"></a> -->
                                <i class="fas fa-user-graduate"></i> Student Login
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/smart_hostel/auth/securitys/security_login.php">
                                <i class="fas fa-user-lock"></i> Security Guard Login
                            </a>
                        </li>
                     </ul>
                   </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <section class="hero">
        <div class="hero-content">
            <h1>Hostel Management System</h1>
            <p>Manage Students, Rooms, Fees, Complaints, Visitors and Events Effortlessly</p>
            <a href="#login" class="btn btn-custom mt-3">Get Started</a>
        </div>
    </section>

    <!-- Feature Section -->
    <section id="features" class="py-5">
        <div class="container text-center">
            <h2 class="fw-bold mb-3">What This Project Does</h2>
            <p class="text-muted mb-5">A Complete System For Managing Hostels Efficiently</p>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="card p-4 feature-box">
                    <div class="feature-icon">
                        <i class="fa-sharp fa-solid fa-user-graduate"></i>
                    </div>
                    <h5>Student Management</h5>
                    <P>Add, Edit and Manage Student With Ease.</P>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-bed"></i>
                    </div>
                    <h5>Room Management</h5>
                    <P>Allocate Rooms, show vacant & occupied rooms.</P>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 feature-box">
                    <div class="feature-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <h5>Search & Record System</h5>
                    <P>Quickly finds student or room data.</P>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 feature-box">
                    <div class="feature-icon">
                        <i class="fa-regular fa-circle-question"></i>
                    </div>
                    <h5>Complaint Handling</h5>
                    <P>Students can submit maintenance or service complaints online.</P>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 feature-box">
                    <div class="feature-icon">
                        <i class="fa-sharp fa-regular fa-clipboard-user"></i>
                    </div>
                    <h5>Visitor Management</h5>
                    <P>Visitor entry and exit records are maintained digitally.</P>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 feature-box">
                    <div class="feature-icon">
                        <i class="fa-regular fa-circle-question"></i>
                    </div>
                    <h5>Attendence Tracking</h5>
                    <P>Record Student entry, exit and leave details digitally.</P>
                </div>
            </div>
        </div>
    </section>

    <section id="login" class="login-section text-center">
        <div class="container">
            <h3 class="fw-bold mb-4">Login to Your Account</h3>

            <a href="/smart_hostel/auth/admins/admin_login.php?role=admin" class="btn btn-primary m-2 px-4">
                <i class="fas fa-user-shield"></i> Admin
            </a>

            <a href="/smart_hostel/auth/students/student_login.php?role=student" class="btn btn-success m-2 px-4">
                <i class="fas fa-user-graduate"></i> Student
            </a>

            <a href="/smart_hostel/auth/securitys/security_login.php?role=security" class="btn btn-dark m-2 px-4">
                <i class="fas fa-shield-alt"></i> Security Guard
            </a>
        </div>
    </section>

    <footer class="text-center">
        <div class="container">
            <p class="mb-0">© 2026 Hostel Management System</p>
        </div>
    </footer>

</body>
</html>

