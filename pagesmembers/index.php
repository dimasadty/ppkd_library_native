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

// Initialize members variable
$members = [];

// Check if the connection is established
$query = "SELECT * FROM members ORDER BY id DESC";
$result = $db_library->query($query);
if ($result) {
    $members = $result->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Query failed: " . $db_library->errorInfo()[2];
}

// Insert members
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

    $stmt = $db_library->prepare("INSERT INTO members (name, birthplace, birthdate, email, phone, gender, address, school, job, socialmedia) VALUES (:name, :birthplace, :birthdate, :email, :phone, :gender, :address, :school, :job, :socialmedia)");
    $stmt->execute([
        ':name' => $name,
        ':birthplace' => $birthplace,
        ':birthdate' => $birthdate,
        ':email' => $email,
        ':phone' => $phone,
        ':gender' => $gender,
        ':address' => $address,
        ':school' => $school,
        ':job' => $job,
        ':socialmedia' => $socialmedia,
    ]);

    header("location:/library/pagesmembers/index.php?notif=success");
    exit;
}

// Delete members
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db_library->prepare("DELETE FROM members WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("location:/library/pagesmembers/index.php?notif=delete-success");
    exit;
}

// Edit members
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $db_library->prepare("SELECT * FROM members WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $dataEdit = $stmt->fetch(PDO::FETCH_ASSOC);
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

    $stmt = $db_library->prepare("UPDATE members SET name = :name, birthplace = :birthplace, birthdate = :birthdate, email = :email, phone = :phone, gender = :gender, address = :address, school = :school, job = :job, socialmedia = :socialmedia WHERE id = :id");
    $stmt->execute([
        ':name' => $name,
        ':birthplace' => $birthplace,
        ':birthdate' => $birthdate,
        ':email' => $email,
        ':phone' => $phone,
        ':gender' => $gender,
        ':address' => $address,
        ':school' => $school,
        ':job' => $job,
        ':socialmedia' => $socialmedia,
        ':id' => $id,
    ]);

    header("location:/library/pagesmembers/index.php?notif=edit-success");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Library PPKD Jakarta Pusat - Members</title>
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
            <h1 class="h3 mb-4 text-gray-800">Members</h1>
            <div class="text-right">
                <a href="/library/pagesmembers/add.php" class="btn btn-primary mb-3">Add More</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Birth Place</th>
                            <th>Birth Date</th>
                            <th>Gender</th>
                            <th>E-mail</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>School</th>
                            <th>Job</th>
                            <th>Social Media</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($members as $dataMembers) { ?>
                        <tr>
                            <td><?php echo $no++ ?></td>
                            <td><?php echo $dataMembers['name'] ?></td>
                            <td><?php echo $dataMembers['birthplace'] ?></td>
                            <td><?php echo $dataMembers['birthdate'] ?></td>
                            <td><?php echo $dataMembers['gender'] ?></td>
                            <td><?php echo $dataMembers['email'] ?></td>
                            <td><?php echo $dataMembers['phone'] ?></td>
                            <td><?php echo $dataMembers['address'] ?></td>
                            <td><?php echo $dataMembers['school'] ?></td>
                            <td><?php echo $dataMembers['job'] ?></td>
                            <td><?php echo $dataMembers['socialmedia'] ?></td>
                            <td>
                                <a href="/library/pagesmembers/view.php?edit=<?php echo $dataMembers['id']; ?>" class="btn btn-secondary btn-sm">View</a>
                                <a href="/library/pagesmembers/add.php?edit=<?php echo $dataMembers['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a onclick="return confirm('Are You Sure to Delete This Data')" href="/library/pagesmembers/index.php?delete=<?php echo $dataMembers['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                        <?php include '../changestatus.php'; ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- main-panel ends -->
    </div>
    <?php include '../inc/footer.php'; ?>
    <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- plugins:js -->
    <?php include '../inc/js.php'; ?>
    <?php include '../inc/modal-logout.php'; ?>
    <!-- End custom js for this page-->
</body>
</html>