<?php
session_start();
include '../config/config.php'; // Adjust the path as necessary
include('../fetch_user.php');

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    header("location:/library/login.php?error-access-failed");
    exit; // Ensure to exit after redirection
}

// Query to get books
$query = "SELECT * FROM books ORDER BY id DESC";
$result = $db_library->query($query);
if ($result) {
    $books = $result->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Query failed: " . $db_library->errorInfo()[2];
}

// If the form is submitted, handle the form data
if (isset($_POST['submit'])) {
    $booksname = $_POST['booksname'];
    $bookstype = $_POST['bookstype'];
    $booksdesc = $_POST['booksdesc'];
    $booksimage = $_POST['booksimage'];

    $insertbooks = $db_library->prepare("INSERT INTO books (booksname, bookstype, booksdesc, booksimage) VALUES (:booksname, :bookstype, :booksdesc, :booksimage)");
    if ($insertbooks->execute([
        $booksname,
        $bookstype,
        $booksdesc,
        $booksimage,
    ])) {
        header("location:/library/pagesbooks/index.php?notif=success");
        exit;
    } else {
        echo "Error: " . $insertbooks->errorInfo()[2];
    }
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

// If the edit parameter is present, get the user data for editing
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editQuery = "SELECT * FROM books WHERE id = ?";
    $stmt = $db_library->prepare($editQuery);
    if ($stmt->execute([$id])) {
        $dataEdit = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "Fetch failed: " . $db_library->errorInfo()[2];
    }
}

// If the edit form is submitted, update the user data
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $booksname = $_POST['booksname'];
    $bookstype = $_POST['bookstype'];
    $booksdesc = $_POST['booksdesc'];
    $booksdesc = $_POST['booksimage'];
    $id = $_GET['edit'];

    $edit = $db_library->prepare("UPDATE books SET booksname=?, bookstype=?, booksdesc=? WHERE id=?");
    if ($edit->execute([
        $booksname,
        $bookstype,
        $booksdesc,
        $booksimage,

    ])) {
        header("location:/library/pagesbooks/index.php?notif=edit-success");
        exit;
    } else {
        echo "Error: " . $edit->errorInfo()[2];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Library PPKD Jakarta Pusat - books </title>
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
            <?php if (isset($_GET['edit'])) { ?>
                <h1 class="h3 mb-4 text-gray-800">books</h1>
                <div class="card">
                    <div class="card-header">Change books</div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name"></label>
                                <input value="<?php echo $dataEdit['booksname'] ?>" type="text" class="form-control" name="booksname" placeholder="books name...">
                            </div>
                            <div class="mb-3">
                                <label for="name"></label>
                                <input value="<?php echo $dataEdit['bookstype'] ?>" type="text" class="form-control" name="bookstype" placeholder="books type...">
                            </div>
                            <div class="mb-3">
                                <label for="name"></label>
                                <input value="<?php echo $dataEdit['booksdesc'] ?>" type="text" class="form-control" name="booksdesc" placeholder="books description...">
                            </div>
                            <div class="mb-3">
                                <label for="name"></label>
                                <input type="text">
                                <input value="<?php echo $dataEdit['booksimage'] ?>" type="file" name="booksimage">
                                </br>
                            </div>
                            <div class="mb-3">
                                <input name="edit" type="submit" class="btn btn-primary" value="submit">
                                <a href="/library/pagesbooks/index.php" class="btn btn-danger">Cancel</a>
                            </div>
                        </form>
                    </div>
                <?php } else { ?>
                    <h1 class="h3 mb-4 text-gray-800">Add More</h1>
                    <div class="card-body">
                        <div class="card-header"></div>
                        <div class="card-body">
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="name"></label>
                                    <input type="text" class="form-control" name="booksname" placeholder="books name...">
                                </div>
                                <div class="mb-3">
                                    <label for="name"></label>
                                    <input type="text" class="form-control" name="bookstype" placeholder="books type...">
                                </div>
                                <div class="mb-3">
                                    <label for="name"></label>
                                    <input type="text" class="form-control" name="booksdesc" placeholder="books description...">
                                </div>
                                <div class="mb-3">
                                    <label for="name"></label>
                                    <input value="<?php echo $dataEdit['booksimage'] ?>" type="file" name="booksimage">
                                    </br>
                                </div>
                                <div class="mb-3">
                                    <input name="submit" type="submit" class="btn btn-primary" value="submit">
                                    <a href="/library/pagesbooks/index.php" class="btn btn-danger">Cancel</a>
                                </div>
                            </form>
                        </div>
                    <?php } ?>
                    </div>
                </div>
        </div>
        <!-- main-panel ends -->
    </div>
    <?php include '../inc/footer.php'; ?>
    <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <?php include '../inc/js.php'; ?>
    <?php include '../inc/modal-logout.php'; ?>
    <!-- End custom js for this page-->
</body>

</html>