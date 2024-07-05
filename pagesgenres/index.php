<?php
session_start();
include '../config/config.php'; // Adjust the path as necessary
include('../fetch_user.php');

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    header("location:/library/login.php?error-access-failed");
    exit; // Ensure to exit after redirection
}
// Query to get genres
$query = "SELECT * FROM genres ORDER BY id DESC";
$result = $db_library->query($query);
$genres = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $genres[] = $row;
    }
} else {
    echo "Query failed: " . $db_library->error;
}

// If the delete parameter is present, delete the level
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $deleteStmt = $db_library->prepare("DELETE FROM genres WHERE id = ?");
    $deleteStmt->bind_param('i', $id); // 'i' indicates the type of the parameter (integer)
    if ($deleteStmt->execute()) {
        header("location:/library/pagesgenres/index.php?notif=delete-success");
        exit;
    } else {
        echo "Error: ". $deleteStmt->error;
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
    <title>Library PPKD Jakarta Pusat - genres</title>
    <!-- plugins:css -->
    <?php include '../inc/css.php'; ?>
</head>

<body class="with-welcome-text">
    <!-- partial:partials/_navbar.html -->
    <?php include '../inc/navbar.php'; ?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <?php include '../inc/sidebar.php'; ?>
        <!-- partial -->
        <!-- main panel -->
        <div class="container-fluid">
            <!-- Page Heading -->
            <h1 class="h3 mb-4 text-gray-800">genres</h1>
            <div class="card">
                <div class="card-body">
                    <div align="right">
                        <a href="/library/pagesgenres/add.php" class="btn btn-primary mb-3">Add More</a>
                    </div>
                    <div class="table responsive">
                        <table class="table table-bordered" id="">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>genres</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($genres as $datagenres) { ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($datagenres['genrename']); ?></td>
                                        <td>
                                            <a href="/library/pagesgenres/add.php?edit=<?php echo $datagenres['id']; ?>"
                                                class="btn btn-primary btn-sm">Change genres</a>
                                            <a onclick="return confirm('Are You Sure To Delete This Data?')"
                                                href="/library/pagesgenres/index.php?delete=<?php echo $datagenres['id']; ?>"
                                                class="btn btn-danger btn-sm">Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- main-panel ends -->
    <?php include '../inc/footer.php'; ?>
    <!-- page-body-wrapper ends -->
    <!-- container-scroller -->
    <!-- plugins:js -->
    <?php include '../inc/js.php'; ?>
    <?php include '../inc/modal-logout.php'; ?>
    <!-- End custom js for this page-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
