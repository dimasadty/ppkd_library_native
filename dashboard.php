<?php
session_start();
include 'config/config.php';
include('fetch_user.php');

// Check if user is logged in
if (!isset($_SESSION['name'])) {
  header("location:/library/login.php?error-access-failed");
  exit; // Ensure to exit after redirection
}

// Example: Check for admin role to show additional menu items
$isAdministrator = ($_SESSION['role'] == 'administrator'); // Adjust according to your role system

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Library PPKD Jakarta Pusat - Dashboard</title>
  <!-- plugins:css -->
  <?php include 'inc/css.php'; ?>
</head>

<body class="with-welcome-text">
  <!-- partial:partials/_navbar.html -->
  <?php include 'inc/navbar.php'; ?>
  <!-- partial -->
  <div class="container-fluid page-body-wrapper">
    <!-- partial:partials/_sidebar.html -->
    <?php include 'inc/sidebar.php'; ?>
    <!-- partial -->
    <!-- main panel -->
    <!-- Example: Show additional admin content -->
    <?php if ($isAdministrator) : ?>
      <div class="admin-content">
        <!-- Additional admin-specific content -->
      </div>
    <?php endif; ?>
    <!-- main-panel ends -->
  </div>
  <?php include 'inc/footer.php'; ?>
  <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <?php include 'inc/js.php'; ?>
  <?php include 'inc/modal-logout.php'; ?>
  <!-- End custom js for this page-->
</body>

</html>