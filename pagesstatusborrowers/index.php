<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    header("location:/library/login.php?error-access-failed");
    exit; // Ensure to exit after redirection
}

// Include configuration file
include '../config/config.php';
include('../fetch_user.php');

// Initialize borrowers variable
$borrowers = [];

// Example query with MySQLi
$query = "SELECT borrowers.*, members.name FROM borrowers LEFT JOIN members ON members.id = borrowers.id_member ORDER BY borrowers.id DESC";
$result = $db_library->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $borrowers[] = $row;
    }
} else {
    echo "Query failed: " . $db_library->error;
}
$query = "SELECT detailed_borrowers.*, borrowers.no_transaction FROM detailed_borrowers LEFT JOIN borrowers ON borrowers.id = detailed_borrowers.id_borrower ORDER BY detailed_borrowers.id DESC";
$result = $db_library->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $detailed_borrowers[] = $row;
    }
} else {
    echo "Query failed: " . $db_library->error;
}


// If the delete parameter is present, delete the user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Delete corresponding rows in the detailed_borrowers table
    $delete_detailed_borrowers = $db_library->prepare("DELETE FROM detailed_borrowers WHERE id_borrower=?");
    $delete_detailed_borrowers->bind_param("i", $id);
    $delete_detailed_borrowers->execute();

    // Delete the borrower
    $delete = $db_library->prepare("DELETE FROM borrowers WHERE id=?");
    $delete->bind_param("i", $id);
    if ($delete->execute()) {
        header("location:/library/pagesstatusborrowers/index.php?notif=delete-success");
        exit;
    } else {
        echo "Error: " . $delete->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Library PPKD Jakarta Pusat - status borrowers</title>
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
        <div class="main-panel">
            <div class="content-wrapper">
                <h1 class="h3 mb-4 text-gray-800">status borrowers</h1>
                <div class="card">
                    <div class="card-body">
                        <div align="right">
                            <!-- <a href="/library/pagesstatusborrowers/add.php" class="btn btn-primary mb-3">Add More</a> -->
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Transaction Number</th>
                                        <th>Borrowers Name</th>
                                        <th>Books Out</th>
                                        <th>Books Return</th>
                                        <th>Fines Status</th>
                                        <th>Fines Total</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($detailed_borrowers as $datadetailed_borrowers) {
                                        // Find matching borrower data
                                        $matching_borrower = null;
                                        foreach ($borrowers as $databorrowers) {
                                            if ($databorrowers['id'] == $datadetailed_borrowers['id_borrower']) {
                                                $matching_borrower = $databorrowers;
                                                break;
                                            }
                                        }
                                        if ($matching_borrower) {
                                    ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo htmlspecialchars($datadetailed_borrowers['no_transaction']); ?></td>
                                                <td><?php echo htmlspecialchars($matching_borrower['name']); ?></td>
                                                <td><?php echo htmlspecialchars($datadetailed_borrowers['booksdate_out']); ?></td>
                                                <td><?php echo htmlspecialchars($datadetailed_borrowers['booksdate_in']); ?></td>
                                                <td>
                                                    <?php
                                                    $booksdate_in = strtotime($datadetailed_borrowers['booksdate_in']);
                                                    // echo $booksdate_in; echo "<br>";
                                                    $booksdate_out = strtotime($datadetailed_borrowers['booksdate_out']);
                                                    $kembali = max(0, ($booksdate_out - $booksdate_in) / (60 * 60 * 24));
                                                    if ($booksdate_in < $booksdate_out) {
                                                        $deskripsi = 'late ' . $kembali . ' days fines';
                                                    } else {
                                                        $deskripsi = 'No description available';
                                                    }
                                                    echo htmlspecialchars($deskripsi);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $fines_per_day = 2250;
                                                    $totalfines = $kembali * $fines_per_day;
                                                    echo "Rp. " . number_format($totalfines, 0, ".", ",");
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($datadetailed_borrowers['description']); ?></td>
                                                <td>
                                                    <a href="/library/pagesstatusborrowers/print.php?edit=<?php echo $datadetailed_borrowers['id']; ?>" class="btn btn-primary btn-sm">Details</a>
                                                    <a onclick="return confirm('Are You Sure to Delete This Data')" href="/library/pagesstatusborrowers/index.php?delete=<?php echo $datadetailed_borrowers['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
    <?php include '../inc/footer.php'; ?>
    <!-- main-panel ends -->
    <?php include '../inc/js.php'; ?>
    <?php include '../inc/modal-logout.php'; ?>
</body>

</html>