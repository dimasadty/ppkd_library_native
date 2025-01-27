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

// Query to fetch members
$query = "SELECT * FROM members ORDER BY id DESC";
$result = $db_library->query($query);
$memberss = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $memberss[] = $row;
    }
} else {
    echo "Query failed: " . $db_library->error;
}

// Insert member
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $birthplace = $_POST['birthplace'];
    $birthdate = $_POST['birthdate'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $school = $_POST['school'];
    $job = $_POST['job'];
    $socialmedia = $_POST['socialmedia'];

    $stmt = $db_library->prepare("INSERT INTO members (name, birthplace, birthdate, email, phone, gender, address, school, job, socialmedia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $name, $birthplace, $birthdate, $email, $phone, $gender, $address, $school, $job, $socialmedia);
    if ($stmt->execute()) {
        header("location:/library/pagesmembers/index.php?notif=success");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Delete member
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db_library->prepare("DELETE FROM members WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("location:/library/pagesmembers/index.php?notif=delete-success");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Edit member
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $db_library->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dataEdit = $result->fetch_assoc();
}

if (isset($_POST['edit'])) {
    $name = $_POST['name'];
    $birthplace = $_POST['birthplace'];
    $birthdate = $_POST['birthdate'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $school = $_POST['school'];
    $job = $_POST['job'];
    $socialmedia = $_POST['socialmedia'];
    $id = $_GET['edit'];

    $stmt = $db_library->prepare("UPDATE members SET name = ?, birthplace = ?, birthdate = ?, email = ?, phone = ?, gender = ?, address = ?, school = ?, job = ?, socialmedia = ? WHERE id = ?");
    $stmt->bind_param("ssssssssssi", $name, $birthplace, $birthdate, $email, $phone, $gender, $address, $school, $job, $socialmedia, $id);
    if ($stmt->execute()) {
        header("location:/library/pagesmembers/index.php?notif=edit-success");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Library PPKD Jakarta Pusat - members</title>
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

        <div class="container">

            <!-- Outer Row -->
            <div class="row justify-content-center">

                <div class="col-xl-10 col-lg-12 col-md-9">

                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="p-5">
                                        <?php if (isset($_GET['edit'])) { ?>

                                            <div class="text-center">
                                                <h1 class="h4 text-gray-900 mb-4">Library Member - PPKD Jakarta Pusat
                                                </h1>
                                            </div>
                                            <form class="user" method="post">
                                                <!-- input form -->
                                                <div class="form-group">
                                                    <input name="name" type="text" class="form-control form-control-user"
                                                        value="<?php echo htmlspecialchars($dataEdit['name']); ?>"
                                                        placeholder="Name...">
                                                </div>
                                                <div class="form-group">
                                                    <input name="birthplace" type="text"
                                                        class="form-control form-control-user"
                                                        value="<?php echo htmlspecialchars($dataEdit['birthplace']); ?>"
                                                        placeholder="Birth Place...">
                                                </div>
                                                <div class="form-group">
                                                    <input name="birthdate" type="date"
                                                        class="form-control form-control-user"
                                                        value="<?php echo htmlspecialchars($dataEdit['birthdate']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <input name="email" class="form-control form-control-user"
                                                        value="<?php echo htmlspecialchars($dataEdit['email']); ?>"
                                                        type="email" placeholder="Email...">
                                                </div>
                                                <div class="form-group">
                                                    <input name="phone" type="tel"
                                                        class="form-control form-control-user"
                                                        value="<?php echo htmlspecialchars($dataEdit['phone']); ?>"
                                                        placeholder="Phone Number...">
                                                </div>
                                                <div class="form-group">
                                                    <input name="gender" type="radio" id="gender" value="Men"
                                                        <?php if ($dataEdit['gender'] == 'Men') echo 'checked'; ?>> Men
                                                    <input name="gender" type="radio" id="gender" value="Women"
                                                        <?php if ($dataEdit['gender'] == 'Women') echo 'checked'; ?>>
                                                    Women
                                                </div>
                                                <div class="form-group">
                                                    <input name="address" type="text"
                                                        class="form-control form-control-user"
                                                        value="<?php echo htmlspecialchars($dataEdit['address']); ?>"
                                                        placeholder="Address...">
                                                </div>
                                                <div class="form-group">
                                                    <input name="school" type="text"
                                                        class="form-control form-control-user"
                                                        value="<?php echo htmlspecialchars($dataEdit['school']); ?>"
                                                        placeholder="Education School...">
                                                </div>
                                                <div class="form-group">
                                                    <input name="job" type="text"
                                                        class="form-control form-control-user"
                                                        value="<?php echo htmlspecialchars($dataEdit['job']); ?>"
                                                        placeholder="Job...">
                                                </div>
                                                <div class="form-group">
                                                    <input name="socialmedia" type="text"
                                                        class="form-control form-control-user"
                                                        value="<?php echo htmlspecialchars($dataEdit['socialmedia']); ?>"
                                                        placeholder="Social Media">
                                                </div>
                                                <button type="submit" name="edit"
                                                    class="btn btn-primary btn-user btn-block">
                                                    Edit Data
                                                </button>
                                                <hr>
                                            </form>
                                        <?php } else { ?>
                                            <div class="text-center">
                                                <h1 class="h4 text-gray-900 mb-4">Library Member - PPKD Jakarta Pusat
                                                </h1>
                                            </div>
                                            <form class="user" method="post">
                                                <!-- input form -->
                                                <div class="form-group">
                                                    <input name="name" type="text" class="form-control form-control-user"
                                                        placeholder="Name...">
                                                </div>
                                                <div class="form-group">
                                                    <input name="birthplace" type="text"
                                                        class="form-control form-control-user"
                                                        placeholder="Birth Place...">
                                                </div>
                                                <div class="form-group">
                                                    <input name="birthdate" type="date"
                                                        class="form-control form-control-user">
                                                </div>
                                                <div class="form-group">
                                                    <input name="email" class="form-control form-control-user"
                                                        type="email" placeholder="Email...">
                                                </div>
                                                <div class="form-group">
                                                    <input name="phone" type="tel"
                                                        class="form-control form-control-user"
                                                        placeholder="Phone Number...">
                                                </div>
                                                <div class="form-group">
                                                    <input name="gender" type="radio" id="gender" value="Men"> Men
                                                    <input name="gender" type="radio" id="gender" value="Women"> Women
                                                </div>
                                                <div class="form-group">
                                                    <input name="address" type="text"
                                                        class="form-control form-control-user"
                                                        placeholder="Address...">
                                                </div>
                                                <div class="form-group">
                                                    <input name="school" type="text"
                                                        class="form-control form-control-user"
                                                        placeholder="Education School...">
                                                </div>
                                                <div class="form-group">
                                                    <input name="job" type="text" class="form-control form-control-user"
                                                        placeholder="Job...">
                                                </div>
                                                <div class="form-group">
                                                    <input name="socialmedia" type="text"
                                                        class="form-control form-control-user"
                                                        placeholder="Social Media...">
                                                </div>
                                                <button type="submit" name="submit"
                                                    class="btn btn-primary btn-user btn-block">
                                                    Submit Data
                                                </button>
                                                <hr>
                                            </form>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <!-- main panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
</body>

</html>
