<?php
session_start();
include '../config/config.php'; // Adjust the path as necessary
include('../fetch_user.php');

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    header("location:/library/login.php?error-access-failed");
    exit;
}

// Query to get books
$query = "SELECT books.*, images.name AS id_booksimage FROM books LEFT JOIN images ON books.id_booksimage = images.id ORDER BY books.id DESC";
$result = $db_library->query($query);
if ($result) {
    $books = $result->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Query failed: " . $db_library->errorInfo()[2];
}

// If the delete parameter is present, delete the user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete = $db_library->prepare("DELETE FROM books WHERE id=?");
    if ($delete->execute([$id])) {
        header("location:/library/pagesbooks/index.php?notif=delete-success");
        exit;
    } else {
        echo "Error: " . $delete->errorInfo()[2];
    }
}

// // If the edit parameter is present, get the user data for editing
// if (isset($_GET['edit'])) {
//     $id = $_GET['edit'];
//     $editQuery = "SELECT * FROM books WHERE id = ?";
//     $stmt = $db_library->prepare($editQuery);
//     if ($stmt->execute([$id])) {
//         $dataEdit = $stmt->fetch(PDO::FETCH_ASSOC);
//     } else {
//         echo "Fetch failed: " . $db_library->errorInfo()[2];
//     }
// }

// // If the edit form is submitted, update the user data
// if (isset($_POST['edit'])) {
//     $id = $_POST['id'];
//     $booksname = $_POST['booksname'];
//     $bookstype = $_POST['bookstype'];
//     $booksdesc = $_POST['booksdesc'];
//     $id_booksimage = $_POST['id_booksimage'];

//     $edit = $db_library->prepare("UPDATE books SET booksname=?, bookstype=?, booksdesc=?, id_booksimage=? WHERE id=?");
//     if ($edit->execute([$booksname, $bookstype, $booksdesc, $id_booksimage, $id])) {
//         header("location:/library/pagesbooks/index.php?notif=edit-success");
//         exit;
//     } else {
//         echo "Error: " . $edit->errorInfo()[2];
//     }
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>  
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Library PPKD Jakarta Pusat - books</title>
    <!-- plugins:css -->
    <?php include '../inc/css.php'; ?>
    <?php include '../inc/styles.css'; ?>
    <style>
        table { 
    table-layout:fixed;
}
td { 
    overflow: hidden; 
    text-overflow: ellipsis; 
    word-wrap: break-word;
}
@media only screen and (max-width: 480px) {
    /* horizontal scrollbar for tables if mobile screen */
    .tablemobile {
        overflow-x: auto;
        display: block;
    }
}
    </style>
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
            <h1 class="h3 mb-4 text-gray-800">books</h1>
            <div align="right">
                <a href="/library/pagesbooks/add.php" class="btn btn-primary mb-3">Add More</a>
            </div>
            <div class="table responsive">
                <table class="table table-bordered table-sm" id="">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>books name</th>
                            <th>books author</th>
                            <th>books publisher</th>
                            <th>books quantity</th>
                            <th>books year</th>
                            <th>books type</th>
                            <th>books description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($books as $dataBooks) { ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $dataBooks['booksname']; ?></td>
                            <td><?php echo $dataBooks['booksauthor']; ?></td>
                            <td><?php echo $dataBooks['bookspublisher']; ?></td>
                            <td><?php echo $dataBooks['booksquantity']; ?></td>
                            <td><?php echo $dataBooks['booksyear']; ?></td>
                            <td><?php echo $dataBooks['bookstype']; ?></td>
                            <td><?php echo $dataBooks['booksdesc']; ?></td>
                            <td>
                                <a href="/library/pagesbooks/add.php?edit=<?php echo $dataBooks['id']; ?>" class="btn btn-primary btn-sm">Change books</a>
                                <a onclick="return confirm('Are You Sure To Delete This Data?')" href="/library/pagesbooks/index.php?delete=<?php echo $dataBooks['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- main-panel ends -->
    </div>
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
