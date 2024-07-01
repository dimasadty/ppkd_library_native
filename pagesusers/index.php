<?php
session_start();
include '../config/config.php'; // Adjust the path as necessary
include('../fetch_user.php');

// Check if session variable is set for authentication
if (!isset($_SESSION['name'])) {
    header("location:/library/index.php?error-access-failed");
    exit; // Exit after redirection
}

// Example query with PDO
$query = "SELECT users.*, levels.level_name FROM users LEFT JOIN levels ON levels.id = users.id_level ORDER BY users.id DESC";
$result = $db_library->query($query);
if ($result) {
    $users = $result->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Query failed: " . $db_library->errorInfo()[2];
}

// Handle delete action if specified
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $db_library->prepare($deleteQuery);
    if ($stmt->execute([$id])) {
        header("location:/library/pagesusers/index.php");
        exit; // Ensure to exit after redirection
    } else {
        echo "Delete failed: " . $db_library->errorInfo()[2];
    }
}

// Handle edit action if specified
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editQuery = "SELECT * FROM users WHERE id = ?";
    $stmt = $db_library->prepare($editQuery);
    if ($stmt->execute([$id])) {
        $dataEdit = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "Fetch failed: " . $db_library->errorInfo()[2];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Library PPKD Jakarta Pusat - users</title>
    <?php include '../inc/css.php'; ?>
</head>
<body class="with-welcome-text">
    <?php include '../inc/navbar.php'; ?>
    <div class="container-fluid page-body-wrapper">
        <?php include '../inc/sidebar.php'; ?>
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Data users</h1>
            <div align="right">
                <a href="../signup.php" class="btn btn-primary mb-3">Tambahkan</a>
            </div>
            <div class="table responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Level</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($users as $datausers) { // Loop through $users array fetched from PDO
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $datausers['name']; ?></td>
                                <td><?php echo $datausers['email']; ?></td>
                                <td><?php echo $datausers['level_name']; ?></td>
                                <td>
                                    <a href="../signup.php?edit=<?php echo $datausers['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a onclick="return confirm('Are You Sure To Delete This Data?')" href="index.php?delete=<?php echo $datausers['id']; ?>" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include '../inc/footer.php'; ?>
    <?php include '../inc/js.php'; ?>
    <?php include '../inc/modal-logout.php'; ?>
</body>
</html>