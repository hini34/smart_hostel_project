<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdminLogin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/admin_login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-11 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                <div class="card shadow-lg p-4">
                    <h3 class="text-center mb-4">Admin Login</h3>

                    <form action="admin_login_process.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                  <i class="fa-solid fa-lock"></i>  
                                </span>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>

                        <!-- <div class="mb-3 text-end">
                            <a href="forgotPass.php" class="text-decoration-none fw-semibold">
                                Forgot Password?
                            </a>
                        </div> -->

                        <button class="btn btn-primary w-100">Login</button>
                        <p class="text-center mt-3">Don't have an account?<a href="admin_register.php">Sign Up</a>
                        </p>

                    </form>
                </div>
            </div>
        </div>
    </div> 
</body>
<script src="assets/js/admin_login.js"></script>
</html>
