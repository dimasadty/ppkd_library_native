<?php
session_start();

// Include necessary files
include '../config/config.php'; // Adjust the path as necessary
include '../fetch_user.php'; // Adjust the path as necessary

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    header("location: /library/login.php?error-access-failed");
    exit;
}

function handleFileUpload($file)
{
    $targetDir = "../assets/admin/images/"; // Adjust the target directory as necessary
    $fileName = basename($file["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');

    // Check if file is a valid upload
    if (!is_uploaded_file($file["tmp_name"])) {
        return "Error: Temporary file is not accessible.";
    }

    // Check file type
    if (!in_array($fileType, $allowTypes)) {
        return "Error: File type is not allowed.";
    }

    // Check file size
    if ($file["size"] > 1024 * 1024) { // 1MB limit
        return "Error: File size exceeds the maximum allowed limit.";
    }

    // Check directory is writable
    if (!is_writable($targetDir)) {
        return "Error: Upload directory is not writable.";
    }

    // Move the file to target directory
    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        // Insert the file name into 'images' table
        global $db_library;
        $insertQuery = "INSERT INTO images (name) VALUES ('$fileName')";
        if (mysqli_query($db_library, $insertQuery)) {
            return mysqli_insert_id($db_library); // Return the ID of the inserted image record
        } else {
            return "Error: Failed to insert image name into database.";
        }
    } else {
        return "Error: File upload failed.";
    }
}

// Query to get books including associated image
$query = "SELECT books.*, images.id AS image_id FROM books LEFT JOIN images ON images.id = books.id_booksimage ORDER BY books.id DESC";
$result = mysqli_query($db_library, $query);
if (!$result) {
    echo "Query failed: " . mysqli_error($db_library);
    exit;
}
$books = [];
while ($row = mysqli_fetch_assoc($result)) {
    $books[] = $row;
}

// Query to get genres
$query = "SELECT books.*, genres.id FROM books LEFT JOIN genres ON genres.id = books.id_genre ORDER BY books.id DESC";
$result = $db_library->query($query);
if ($result) {
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
} else {
    echo "Query failed: " . $db_library->error;
}

// Query to get location
$query = "SELECT books.*, locations.id FROM books LEFT JOIN locations ON locations.id = books.id_location ORDER BY books.id DESC";
$result = $db_library->query($query);
if ($result) {
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
} else {
    echo "Query failed: " . $db_library->error;
}

// Handle book deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $deleteQuery = "DELETE FROM books WHERE id=$id";
    if (mysqli_query($db_library, $deleteQuery)) {
        header("location: /library/pagesbooks/index.php?notif=delete-success");
        exit;
    } else {
        echo "Error: " . mysqli_error($db_library);
    }
}

// Handle editing book details
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editQuery = "SELECT * FROM books WHERE id = $id";
    $result = mysqli_query($db_library, $editQuery);
    if ($result) {
        $dataEdit = mysqli_fetch_assoc($result);
    } else {
        echo "Fetch failed: " . mysqli_error($db_library);
    }
}

// Process form submission for editing a book
if (isset($_POST['edit'])) {
    if (!isset($_GET['edit'])) {
        header("location: /library/pagesbooks/index.php?notif=error");
        exit;
    }
    $id = $_GET['edit'];
    $booksname = trim($_POST['booksname']);
    $booksauthor = trim($_POST['booksauthor']);
    $bookspublisher = trim($_POST['bookspublisher']);
    $booksquantity = (int) $_POST['booksquantity'];
    $booksyear = (int) $_POST['booksyear'];
    $id_genre = trim($_POST['id_genre']);
    $booksdesc = trim($_POST['booksdesc']);
    $id_location = trim($_POST['id_location']);

    // Validate input data
    if (empty($booksname) || empty($booksauthor) || empty($bookspublisher) || empty($booksquantity) || empty($booksyear) || empty($id_genre) || empty($booksdesc) || empty($id_location)) {
        header("location: /library/pagesbooks/index.php?notif=error");
        exit;
    }

    // Handle file upload if a new file is selected
    $id_booksimage = $dataEdit['id_booksimage']; // Default to existing image
    if (isset($_FILES['id_booksimage']) && $_FILES['id_booksimage']['size'] > 0) {
        $uploadedFile = handleFileUpload($_FILES['id_booksimage']);
        if (!is_numeric($uploadedFile)) {
            header("location: /library/pagesbooks/index.php?notif=file-upload-error");
            exit;
        }
        $id_booksimage = $uploadedFile;
    }

    // Update the book record in the database
    $updateQuery = "UPDATE books SET booksname='$booksname', booksauthor='$booksauthor', bookspublisher='$bookspublisher', booksquantity=$booksquantity, booksyear=$booksyear, id_genre='$id_genre', booksdesc='$booksdesc', id_location='$id_location',id_booksimage=$id_booksimage WHERE id=$id";
    if (mysqli_query($db_library, $updateQuery)) {
        header("location: /library/pagesbooks/index.php?notif=edit-success");
        exit;
    } else {
        echo "Error: " . mysqli_error($db_library);
    }
}

// Process form submission for adding a new book
if (isset($_POST['submit'])) {
    $booksname = trim($_POST['booksname']);
    $booksauthor = trim($_POST['booksauthor']);
    $bookspublisher = trim($_POST['bookspublisher']);
    $booksquantity = (int) $_POST['booksquantity'];
    $booksyear = (int) $_POST['booksyear'];
    $id_genre = trim($_POST['id_genre']);
    $booksdesc = trim($_POST['booksdesc']);
    $id_location = trim($_POST['id_location']);

    // Validate input data
    if (empty($booksname) || empty($booksauthor) || empty($bookspublisher) || empty($booksquantity) || empty($booksyear) || empty($id_genre) || empty($booksdesc) || empty($id_location)) {
        header("location: /library/pagesbooks/index.php?notif=error");
        exit;
    }

    // Handle file upload for new book
    $id_booksimage = "NULL"; // Default value
    if (isset($_FILES['id_booksimage']) && $_FILES['id_booksimage']['size'] > 0) {
        $uploadedFile = handleFileUpload($_FILES['id_booksimage']);
        if (!is_numeric($uploadedFile)) {
            header("location: /library/pagesbooks/index.php?notif=file-upload-error");
            exit;
        }
        $id_booksimage = $uploadedFile;
    }

    // Insert new book record into the database
    $insertQuery = "INSERT INTO books (booksname, booksauthor, bookspublisher, booksquantity, booksyear, id_genre, booksdesc, id_booksimage, id_location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($db_library, $insertQuery);
    mysqli_stmt_bind_param($stmt, "sssiisssi", $booksname, $booksauthor, $bookspublisher, $booksquantity, $booksyear, $id_genre, $booksdesc, $id_booksimage, $id_location);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("location: /library/pagesbooks/index.php?notif=add-success");
        exit;
    } else {
        echo "Error: " . mysqli_error($db_library);
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Library PPKD Jakarta Pusat - Books </title>
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
                <h1 class="h3 mb-4 text-gray-800">Edit Book</h1>
                <div class="card">
                    <div class="card-header">Edit Book Details</div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="booksname" class="form-label">Book Name</label>
                                <input type="text" class="form-control" id="booksname" name="booksname" value="<?php echo htmlspecialchars($dataEdit['booksname']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="booksauthor" class="form-label">Author</label>
                                <input type="text" class="form-control" id="booksauthor" name="booksauthor" value="<?php echo htmlspecialchars($dataEdit['booksauthor']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="bookspublisher" class="form-label">Publisher</label>
                                <input type="text" class="form-control" id="bookspublisher" name="bookspublisher" value="<?php echo htmlspecialchars($dataEdit['bookspublisher']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="booksquantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="booksquantity" name="booksquantity" value="<?php echo $dataEdit['booksquantity']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="booksyear" class="form-label">Year</label>
                                <input type="date" class="form-control" id="booksyear" name="booksyear" value="<?php echo $dataEdit['booksyear']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="id_genre" class="form-label">Genre</label>
                                <select class="form-select form-select-lg" name="id_genre" id="id_genre" class="form-control">
                                    <option value="">Select genres</option>
                                    <?php
                                    $querygenres = $db_library->query("SELECT * FROM genres");
                                    while ($datagenres = $querygenres->fetch_assoc()) {
                                    ?>
                                        <option value="<?php echo $datagenres['id']; ?>">
                                            <?php echo $datagenres['genrename']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="booksdesc" class="form-label">Description</label>
                                <textarea class="form-control" id="booksdesc" name="booksdesc" rows="3" required><?php echo htmlspecialchars($dataEdit['booksdesc']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="id_location" class="form-label">Locations</label>
                                <select class="form-select form-select-lg" name="id_location" id="id_location" class="form-control">
                                    <option value="">Select location</option>
                                    <?php
                                    $querylocations = $db_library->query("SELECT * FROM locations");
                                    while ($datalocations = $querylocations->fetch_assoc()) {
                                    ?>
                                        <option value="<?php echo $datalocations['id']; ?>">
                                            <?php echo $datalocations['locationcodes']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="id_booksimage" class="form-label">Upload Image</label>
                                <input type="file" class="form-control" id="id_booksimage" name="id_booksimage">
                            </div>
                            <button type="submit" name="edit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            <?php } else { ?>
                <!-- Add Book Form -->
                <h1 class="h3 mb-4 text-gray-800">Add New Book</h1>
                <div class="card">
                    <div class="card-header">Add Book Details</div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="booksname" class="form-label">Book Name</label>
                                <input type="text" class="form-control" id="booksname" name="booksname" required>
                            </div>
                            <div class="mb-3">
                                <label for="booksauthor" class="form-label">Author</label>
                                <input type="text" class="form-control" id="booksauthor" name="booksauthor" required>
                            </div>
                            <div class="mb-3">
                                <label for="bookspublisher" class="form-label">Publisher</label>
                                <input type="text" class="form-control" id="bookspublisher" name="bookspublisher" required>
                            </div>
                            <div class="mb-3">
                                <label for="booksquantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="booksquantity" name="booksquantity" required>
                            </div>
                            <div class="mb-3">
                                <label for="booksyear" class="form-label">Year</label>
                                <input type="number" class="form-control" id="booksyear" name="booksyear" required>
                            </div>
                            <div class="mb-3">
                                <label for="id_genre" class="form-label">Genre</label>
                                <select class="form-select form-select-lg" name="id_genre" id="id_genre" class="form-control">
                                    <option value="">Select genres</option>
                                    <?php
                                    $querygenres = $db_library->query("SELECT * FROM genres");
                                    while ($datagenres = $querygenres->fetch_assoc()) {
                                    ?>
                                        <option value="<?php echo $datagenres['id']; ?>">
                                            <?php echo $datagenres['genrename']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="booksdesc" class="form-label">Description</label>
                                <textarea class="form-control" id="booksdesc" name="booksdesc" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="id_location" class="form-label">Locations</label>
                                <select class="form-select form-select-lg" name="id_location" id="id_location" class="form-control">
                                    <option value="">Select location</option>
                                    <?php
                                    $querylocations = $db_library->query("SELECT * FROM locations");
                                    while ($datalocations = $querylocations->fetch_assoc()) {
                                    ?>
                                        <option value="<?php echo $datalocations['id']; ?>">
                                            <?php echo $datalocations['locationcodes']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="id_booksimage" class="form-label">Upload Image</label>
                                <input type="file" class="form-control" id="id_booksimage" name="id_booksimage">
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Add Book</button>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
        <!-- main panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
    <!-- partial:partials/_footer.html -->
    <?php include '../inc/footer.php'; ?>
    <!-- partial -->
    <!-- plugins:js -->
    <?php include '../inc/js.php'; ?>
</body>

</html>