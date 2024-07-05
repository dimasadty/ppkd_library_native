<?php
session_start();
include '../config/config.php'; // Adjust the path as necessary
include '../fetch_user.php'; // Make sure fetch_user.php ends with a semicolon

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    header("location:/library/login.php?error-access-failed");
    exit;
}

// Query to get books with images, genres, and locations
$query = "SELECT books.*, images.name AS id_booksimage, genres.id AS id_genre, locations.id AS id_location 
          FROM books 
          LEFT JOIN images ON books.id_booksimage = images.id 
          LEFT JOIN genres ON genres.id = books.id_genre 
          LEFT JOIN locations ON locations.id = books.id_location 
          ORDER BY books.id DESC";

$result = $db_library->query($query);

$books = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
} else {
    echo "Query failed: " . $db_library->error;
}

// If the delete parameter is present, delete the book
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete = $db_library->prepare("DELETE FROM books WHERE id=?");
    $delete->bind_param("i", $id);
    if ($delete->execute()) {
        header("location:/library/pagesbooks/index.php?notif=delete-success");
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
    <title>Library PPKD Jakarta Pusat - Books</title>
    <!-- Include Bootstrap CSS -->
    <?php include '../inc/css.php'; ?>
    
    <!-- Custom Styles -->
    <style>
        table {
            table-layout: fixed;
        }

        td {
            overflow: hidden;
            text-overflow: ellipsis;
            word-wrap: break-word;
        }

        table td img {
            max-width: 100%; /* Ensure image doesn't exceed its container width */
            height: auto; /* Maintain aspect ratio */
            border-radius: 0; /* Ensure no rounded corners */
        }

        @media only screen and (max-width: 480px) {
            .table-responsive {
                overflow-x: auto;
                display: block;
            }
        }
    </style>
</head>

<body class="with-welcome-text">
    <!-- Include Navbar -->
    <?php include '../inc/navbar.php'; ?>

    <div class="container-fluid page-body-wrapper">
        <!-- Include Sidebar -->
        <?php include '../inc/sidebar.php'; ?>

        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Books</h1>
            <div class="card">
                <div class="card-body">
                    <div align="right">
                        <a href="/library/pagesbooks/add.php" class="btn btn-primary mb-3">Add More</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Books Image</th>
                                    <th>Books Name</th>
                                    <th>Books Author</th>
                                    <th>Books Publisher</th>
                                    <th>Books Quantity</th>
                                    <th>Books Year</th>
                                    <th>Books Type</th>
                                    <th>Books Description</th>
                                    <th>Books Location</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($books as $dataBooks) { ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td class="center"><img src="../assets/admin/images/<?php echo $dataBooks['id_booksimage']; ?>"></td>
                                        <td><?php echo $dataBooks['booksname']; ?></td>
                                        <td><?php echo $dataBooks['booksauthor']; ?></td>
                                        <td><?php echo $dataBooks['bookspublisher']; ?></td>
                                        <td><?php echo $dataBooks['booksquantity']; ?></td>
                                        <td><?php echo $dataBooks['booksyear']; ?></td>
                                        <td><?php echo $dataBooks['id_genre']; ?></td>
                                        <td><?php echo $dataBooks['booksdesc']; ?></td>
                                        <td><?php echo $dataBooks['id_location']; ?></td>
                                        <td>
                                            <a href="/library/pagesbooks/add.php?edit=<?php echo $dataBooks['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                            <a onclick="return confirm('Are You Sure To Delete This Data?')" href="/library/pagesbooks/index.php?delete=<?php echo $dataBooks['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
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

    <!-- Include Footer -->
    <?php include '../inc/footer.php'; ?>

    <!-- Include Scripts -->
    <?php include '../inc/js.php'; ?>
</body>

</html>