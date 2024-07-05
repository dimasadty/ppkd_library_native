<?php
session_start();
include 'config/config.php'; // Adjust the path as necessary

if (!isset($_SESSION['name'])) {
    header("location:../library/login.php?error-access-failed");
    exit;
}
$query = "SELECT users.*, levels.level_name FROM users LEFT JOIN levels ON levels.id = users.id_level ORDER BY users.id DESC";
$result = $db_library->query($query);
if ($result) {
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    echo "Query failed: " . $db_library->error;
}

// If the form is submitted, handle the form data
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $id_level = $_POST['id_level'];
    $password = sha1($_POST['password']); // Hash the password using SHA-1

    $insertusers = $db_library->prepare("INSERT INTO users (name, email, id_level, password) VALUES (?, ?, ?, ?)");
    $insertusers->bind_param("ssis", $name, $email, $id_level, $password); // Bind parameters
    if ($insertusers->execute()) {
        header("location:/library/pagesusers/index.php?notif=success");
        exit;
    } else {
        echo "Error: " . $insertusers->error;
    }
    $insertusers->close(); // Close statement
}

// If the delete parameter is present, delete the users
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete = $db_library->prepare("DELETE FROM users WHERE id=?");
    $delete->bind_param("i", $id); // Bind parameters
    if ($delete->execute()) {
        header("location:users.php?notif=delete-success");
        exit;
    } else {
        echo "Error: " . $delete->error;
    }
    $delete->close(); // Close statement
}

// If the edit parameter is present, get the users data for editing
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editQuery = "SELECT * FROM users WHERE id = ?";
    $stmt = $db_library->prepare($editQuery);
    $stmt->bind_param("i", $id); // Bind parameters
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $dataEdit = $result->fetch_assoc();
    } else {
        echo "Fetch failed: " . $db_library->error;
    }
    $stmt->close(); // Close statement
}

// If the edit form is submitted, update the users data
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $id_level = $_POST['id_level'];
    $password = sha1($_POST['password']); // Hash the password using SHA-1

    $edit = $db_library->prepare("UPDATE users SET name=?, email=?, id_level=?, password=? WHERE id=?");
    $edit->bind_param("ssisi", $name, $email, $id_level, $password, $id); // Bind parameters
    if ($edit->execute()) {
        header("location:/library/pagesusers/index.php?notif=edit-success");
        exit;
    } else {
        echo "Error: " . $edit->error;
    }
    $edit->close(); // Close statement
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Library PPKD Jakarta Pusat - Register</title>
    <!-- plugins:css -->
    <?php include 'inc/css.php'; ?>
    <!-- endinject -->
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                <img src="/library/assets/admin/images/jakarta.png"  alt="logo">
                            </div>
                            <?php if (isset($_GET['edit'])) { ?>
                                <h4>Edit users</h4>
                                <h6 class="fw-light">Update users information</h6>
                                <form class="pt-3" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $dataEdit['id']; ?>">
                                    <div class="form-group">
                                        <input name="name" type="text" class="form-control form-control-lg" id="name" value="<?php echo $dataEdit['name']; ?>" placeholder="Name...">
                                    </div>
                                    <div class="form-group">
                                        <input name="email" type="email" class="form-control form-control-lg" id="exampleInputEmail" value="<?php echo $dataEdit['email']; ?>" placeholder="E-mail...">
                                    </div>
                                    <div class="form-group">
                                        <select class="form-select form-select-lg" name="id_level" id="id_level" class="form-control">
                                            <option value="">Select levels</option>
                                            <?php
                                            $querylevels = $db_library->query("SELECT * FROM levels");
                                            while ($datalevels = $querylevels->fetch_assoc()) {
                                            ?>
                                                <option value="<?php echo $datalevels['id']; ?>" <?php if ($dataEdit['id_level'] == $datalevels['id']) echo 'selected'; ?>>
                                                    <?php echo $datalevels['level_name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input name="password" type="password" class="form-control form-control-lg" id="password" placeholder="Password...">
                                    </div>
                                    <div class="mb-4">
                                        <div class="form-check">
                                            <label class="form-check-label text-muted">
                                                <input type="checkbox" class="form-check-input"> I agree to all Terms & Conditions
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-grid gap-2">
                                        <button name="edit" type="submit" class="btn btn-primary">Edit</button>
                                        <a href="../library/pagesusers/index.php" class="btn btn-danger">Cancel</a>
                                    </div>
                                </form>
                            <?php } else { ?>
                                <h4>New here?</h4>
                                <h6 class="fw-light">Signing up is easy. It only takes a few steps</h6>
                                <form class="pt-3" method="POST">
                                    <div class="form-group">
                                        <input name="name" type="text" class="form-control form-control-lg" id="name" placeholder="Name...">
                                    </div>
                                    <div class="form-group">
                                        <input name="email" type="email" class="form-control form-control-lg" id="exampleInputEmail" placeholder="E-mail...">
                                    </div>
                                    <div class="form-group">
                                        <select class="form-select form-select-lg" name="id_level" id="id_level" class="form-control">
                                            <option value="">Select levels</option>
                                            <?php
                                            $querylevels = $db_library->query("SELECT * FROM levels");
                                            while ($datalevels = $querylevels->fetch_assoc()) {
                                            ?>
                                                <option value="<?php echo $datalevels['id']; ?>">
                                                    <?php echo $datalevels['level_name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input name="password" type="password" class="form-control form-control-lg" id="password" placeholder="Password...">
                                    </div>
                                    <div class="mb-4">
                                        <div class="form-check">
                                            <label class="form-check-label text-muted">
                                                <input type="checkbox" class="form-check-input"> I agree to all Terms & Conditions
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-grid gap-2">
                                        <button name="submit" type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'inc/footer.php'; ?>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <?php include 'inc/js.php'; ?>
    <!-- endinject -->
</body>

</html>
