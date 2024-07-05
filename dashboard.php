<?php
session_start();
include 'config/config.php';
include 'fetch_user.php'; // Include your fetch_user.php file

// Check if user is logged in
if (!isset($_SESSION['name'])) {
  header("location: /library/login.php?error-access-failed");
  exit; // Ensure to exit after redirection
}

// // Example: Check for admin role to show additional menu items
// $isAdministrator = ($_SESSION['role'] == 'administrator'); // Adjust according to your role system

// Example queries using MySQLi
$booksCount = 0;
$borrowersCount = 0;
$membersCount = 0;

// Query to get count of books listed
$querybooks = "SELECT COUNT(*) as total FROM books";
$resultbooks = $db_library->query($querybooks);
if ($resultbooks) {
  $row = $resultbooks->fetch_assoc();
  $booksCount = $row['total'];
} else {
  echo "Query failed: " . $db_library->error;
}

// Query to get count of issued books not returned
$queryborrowers = "SELECT COUNT(*) as total FROM borrowers WHERE id = 0";
$resultborrowers = $db_library->query($queryborrowers);
if ($resultborrowers) {
  $row = $resultborrowers->fetch_assoc();
  $borrowersCount = $row['total'];
} else {
  echo "Query failed: " . $db_library->error;
}

// Query to get count of registered members
$querymembers = "SELECT COUNT(*) as total FROM members";
$resultmembers = $db_library->query($querymembers);
if ($resultmembers) {
  $row = $resultmembers->fetch_assoc();
  $userCount = $row['total'];
} else {
  echo "Query failed: " . $db_library->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Library PPKD Jakarta Pusat - Dashboard</title>
  <?php include 'inc/css.php'; ?>
</head>
<body class="with-welcome-text">
  <?php include 'inc/navbar.php'; ?>
  <div class="container-fluid page-body-wrapper">
    <?php include 'inc/sidebar.php'; ?>
    <div class="content-wrapper">
      <div class="container">
        <div class="row pad-botm">
          <div class="col-md-12">
            <h4 class="header-line"></h4>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3 col-sm-3 col-xs-6">
            <a href="/library/pagesbooks/index.php" class="text-decoration-none">
              <div class="alert alert-secondary back-widget-set text-center">
                <i class="fa fa-book fa-5x"></i>
                <h3><?php echo $booksCount; ?></h3>
                Books Listed
              </div>
            </a>
          </div>

          <div class="col-md-3 col-sm-3 col-xs-6">
            <a href="/library/pagesborrowers/index.php" class="text-decoration-none">
              <div class="alert alert-secondary back-widget-set text-center">
                <i class="fa fa-recycle fa-5x"></i>
                <h3><?php echo $borrowersCount; ?></h3>
                Books Not Returned Yet
              </div>
            </a>
          </div>

          <div class="col-md-3 col-sm-3 col-xs-6">
            <a href="/library/pagesmembers/index.php" class="text-decoration-none">
              <div class="alert alert-secondary back-widget-set text-center">
                <i class="fa fa-users fa-5x"></i>
                <h3><?php echo $membersCount; ?></h3>
                Registered members
              </div>
            </a>
          </div>

          <div class="col-md-3 col-sm-3 col-xs-6">
            <a href="manage-authors.php" class="text-decoration-none">
              <div class="alert alert-secondary back-widget-set text-center">
                <i class="fa fa-user fa-5x"></i>
                <h3>#</h3>
                Authors Listed
              </div>
            </a>
          </div>
        </div>

        <div class="row">
          <div class="col-md-3 col-sm-3 col-xs-6">
            <a href="manage-categories.php" class="text-decoration-none">
              <div class="alert alert-secondary back-widget-set text-center">
                <i class="fa fa-file-archive-o fa-5x"></i>
                <h3>#</h3>
                Listed Categories
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php if ($isAdministrator) : ?>
    <div class="admin-content">
      <!-- Additional admin-specific content -->
    </div>
  <?php endif; ?>
  <?php include 'inc/footer.php'; ?>
  <?php include 'inc/js.php'; ?>
  <?php include 'inc/modal-logout.php'; ?>
</body>
</html>
