<?php
session_start();
include '../config/config.php'; // Adjust the path as necessary
include('../fetch_user.php');

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    header("location:/library/login.php?error-access-failed");
    exit; // Ensure to exit after redirection
}
// Query to get locations
$query = "SELECT * FROM locations ORDER BY id DESC";
$result = $db_library->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
} else {
    echo "Query failed: " . $db_library->error;
}

// If the form is submitted, handle the form data
if (isset($_POST['submit'])) {
    $locationcodes = $_POST['locationcodes'];
    $label = $_POST['label'];
    $rack = $_POST['rack'];

    $insertlocations = $db_library->prepare("INSERT INTO locations (locationcodes, label, rack) VALUES (?,?,?)");
    $insertlocations->bind_param("sss", $locationcodes, $label, $rack); // "s" indicates the type of parameter (string)
    if ($insertlocations->execute()) {
        header("location:/library/pageslocations/index.php?notif=success");
        exit;
    } else {
        echo "Error: " . $insertlocations->error;
    }
}

// If the delete parameter is present, delete the locations
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete = $db_library->prepare("DELETE FROM locations WHERE id=?");
    $delete->bind_param("i", $id); // "i" indicates the type of parameter (integer)
    if ($delete->execute()) {
        header("location:/library/pageslocations/index.php?notif=delete-success");
        exit;
    } else {
        echo "Error: " . $delete->error;
    }
}

// If the edit parameter is present, get the locations data for editing
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editQuery = "SELECT * FROM locations WHERE id = ?";
    $stmt = $db_library->prepare($editQuery);
    $stmt->bind_param("i", $id); // "i" indicates the type of parameter (integer)
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $dataEdit = $result->fetch_assoc();
        } else {
            echo "locations not found.";
        }
    } else {
        echo "Fetch failed: " . $db_library->error;
    }
}

// If the edit form is submitted, update the locations data
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $locationcodes = $_POST['locationcodes'];

    $edit = $db_library->prepare("UPDATE locations SET locationcodes=? WHERE id=?");
    $edit->bind_param("si", $locationcodes, $id); // "si" indicates the types of parameters (string, integer)
    if ($edit->execute()) {
        header("location:/library/pageslocations/index.php?notif=edit-success");
        exit;
    } else {
        echo "Error: " . $edit->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Library PPKD Jakarta Pusat - locations</title>
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
            <h1 class="h3 mb-4 text-gray-800">Edit locations</h1>
            <div class="card">
                <div class="card-header">Edit locations</div>
                <div class="card-body">
                    <form action="" method="post">
                        <input type="hidden" name="id" value="<?php echo $dataEdit['id']; ?>">
                        <div class="mb-3">
                            <label for="locationcodes">locations Name</label>
                            <input value="<?php echo $dataEdit['locationcodes']; ?>" type="text" class="form-control"
                                name="locationcodes" placeholder="Enter locations Name">
                        </div>
                        <div class="mb-3">
                            <label for="label">locations Name</label>
                            <input value="<?php echo $dataEdit['label']; ?>" type="text" class="form-control"
                                name="label" placeholder="Enter label Name">
                        </div>
                        <div class="mb-3">
                            <label for="rack">locations Name</label>
                            <input value="<?php echo $dataEdit['rack']; ?>" type="text" class="form-control"
                                name="rack" placeholder="Enter rack Name">
                        </div>
                        <div class="mb-3">
                            <input name="edit" type="submit" class="btn btn-primary" value="Submit">
                            <a href="/library/pageslocations/index.php" class="btn btn-danger">Cancel</a>
                        </div>
                    </form>
                </div>
                <?php } else { ?>
                <h1 class="h3 mb-4 text-gray-800">Add locations</h1>
                <div class="card-body">
                    <div class="card-header">Add locations</div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="locationcodes">locations Name</label>
                                <input type="text" class="form-control" name="locationcodes"
                                    placeholder="Enter locations Name">
                            </div>
                            <div class="mb-3">
                                <label for="label">label</label>
                                <input type="text" class="form-control" name="label"
                                    placeholder="Enter locations Name">
                            </div>
                            <div class="mb-3">
                                <label for="rack">rack</label>
                                <input type="text" class="form-control" name="rack"
                                    placeholder="Enter locations Name">
                            </div>
                            <div class="mb-3">
                                <input name="submit" type="submit" class="btn btn-primary" value="Submit">
                                <a href="/library/pageslocations/index.php" class="btn btn-danger">Cancel</a>
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
