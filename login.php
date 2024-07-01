<?php
session_start();
include 'config/config.php'; // Include your database connection or configuration file

if (isset($_POST['submit'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    $query = "SELECT * FROM users WHERE email = ?";
    $result = $db_library->prepare($query);
    $result->bindParam(1, $email);
    $result->execute();

    if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        // Verify password (assuming SHA-1 hashing for this example)
        if (sha1($password) === $row['password']){
            // Authentication successful, set sessi on variables
            if (isset($row["id_level"]) == 4) {
                $_SESSION['name'] = $row['name']; // Example session variable
                $_SESSION['id_level'] = $row['id_level']; // Example session variable
    
                // Redirect to dashboard or another secure page
                header("Location: /library/dashboard.php");
                exit;
            } else if (isset($row["id_level"]) == 5) {
                $_SESSION['name'] = $row['name']; // Example session variable
                $_SESSION['id_level'] = $row['id_level']; // Example session variable
    
                // Redirect to dashboard or another secure page
                header("Location: /library/dashboardinstructor.php");
                exit;
            } else if (isset($row["id_level"]) == 6) {
                $_SESSION['name'] = $row['name']; // Example session variable
                $_SESSION['id_level'] = $row['id_level']; // Example session variable
    
                // Redirect to dashboard or another secure page
                header("Location: /library/dashboardinstructor.php");
                exit;
            }

        } else {
            // Invalid password
            header("Location: /library/login.php?error-access-failed");
            exit;
        }
    } else {
        // users not found
        header("Location: /library/login.php?error-access-failed");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Library PPKD Jakarta Pusat - Login</title>
    <!-- plugins:css -->
    <?php include 'inc/css.php'; ?>
    <!-- endinject -->
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                <img src="/library/assets/admin/images/jakarta.png" alt="logo">
                            </div>
                            <h4>Hello! let's get started</h4>
                            <h6 class="fw-light">Sign in to continue.</h6>
                            <form class="pt-3" method="post">
                                <!-- input form -->
                                <div class="form-group">
                                    <input name="email" type="email" class="form-control form-control-users" id="email" placeholder="Enter your email">
                                </div>
                                <div class="form-group">
                                    <input name="password" type="password" class="form-control form-control-users" id="password" placeholder="Enter your password">
                                </div>
                                <div class="mt-3 d-grid gap-2">
                                    <input name="submit" type="submit" class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn" value="Submit">
                                </div>
                                <div class="my-2 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <label class="form-check-label text-muted">
                                            <input type="checkbox" class="form-check-input"> Keep me signed in
                                        </label>
                                    </div>
                                    <a href="#" class="auth-link text-black">Forgot password?</a>
                                </div>
                                <div class="text-center mt-4 fw-light"> Have a problem? <a href="register.html" class="text-primary">Contact Us</a></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <?php include 'inc/footer.php'; ?>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <?php include 'inc/js.php'; ?>
    <!-- endinject -->
</body>
</html>