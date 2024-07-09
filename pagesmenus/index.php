<?php
session_start();

// Include configuration file
include '../config/config.php';

// Query to get menus with levels
$query_menus = "SELECT menus.*, levels.id AS level_id, levels.level_name 
               FROM menus
               LEFT JOIN levels ON menus.id_level = levels.id 
               ORDER BY menus.id DESC";

$result_menus = mysqli_query($db_library, $query_menus);

$menus = [];
if ($result_menus) {
    while ($row = mysqli_fetch_assoc($result_menus)) {
        $menus[] = $row;
    }
} else {
    die("Query failed: " . mysqli_error($db_library));
}

// Dummy array for levels (replace with actual data from database)
$levels = array(
    array('id' => 1, 'level_name' => 'Level 1'),
    array('id' => 2, 'level_name' => 'Level 2'),
    array('id' => 3, 'level_name' => 'Level 3'),
    array('id' => 4, 'level_name' => 'Admin'), // Added based on your form data
    array('id' => 5, 'level_name' => 'User')   // Added based on your form data
);

// Initialize $menuEdt with default values to avoid undefined variable warnings
$menuEdt = array(
    'id' => '',
    'id_level' => '',
    'menu_name' => '',
    'url' => '',
    'icon' => '',
    'num_columns' => 0,
    'make_table' => '',
    'column1' => ''
);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['store']) && $_POST['store'] === "Simpan") {
        // Process insert form submission
        // Ensure to sanitize and validate inputs
        $id_level = $_POST['id_level'];
        $menu_name = htmlspecialchars($_POST['menu_name']);
        $url = htmlspecialchars($_POST['url']);
        $icon = htmlspecialchars($_POST['icon']);
        $make_table = $_POST['make_table'];
        $num_columns = ($make_table === 'yes') ? (int)$_POST['num_columns'] : 0;

        // Prepare insert query
        $query_insertMenu = "INSERT INTO menus (id_level, menu_name, url, icon, num_columns, make_table, created_at, updated_at) 
                             VALUES (?, ?, ?, ?, ?, ?, now(), now())";
        $stmt = mysqli_prepare($db_library, $query_insertMenu);

        // Bind parameters
        mysqli_stmt_bind_param($stmt, 'isssis', $id_level, $menu_name, $url, $icon, $num_columns, $make_table);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Check for success or failure
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            // Success: Redirect or display success message
            header("Location: index.php?page=menu&store=success");
            exit();
        } else {
            // Error: Handle the error (e.g., display error message)
            die("Error inserting data into menus: " . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['update']) && $_POST['update'] === "Edit") {
        // Process update form submission (Not included in this snippet)
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        // Process delete action (Not included in this snippet)
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Library PPKD Jakarta Pusat - Settings</title>
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
        <!-- Content Wrapper -->
        <div class="container-fluid">
            <!-- Main Content -->
            <div class="content">
                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Content Row -->
                    <?php if (isset($_GET['page']) && $_GET['page'] === "dashboard") { ?>
                        <!-- Page Heading -->
                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                        </div>
                    <?php } else if (isset($_GET['page']) && $_GET['page'] === "menu") { ?>
                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                            <h1 class="h3 mb-0 text-gray-800">Menu</h1>
                        </div>

                        <!-- Card Menu nya -->
                        <div class="card">
                            <div class="card-body">
                                <a href="?page=createMenu" class="btn btn-primary btn-sm mb-4">ADD MORE</a>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Level</th>
                                                <th>Menu</th>
                                                <th>URL</th>
                                                <th>Icon</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            foreach ($menus as $menu) { ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= $menu['level_name'] ?></td>
                                                    <td><?= $menu['menu_name'] ?></td>
                                                    <td><?= $menu['url'] ?></td>
                                                    <td><i class="<?= $menu['icon'] ?>"> </i><?= $menu['icon'] ?></td>
                                                    <td class="text-center">
                                                        <a class="btn btn-success btn-sm" href="?page=editMenu&ed=<?= htmlspecialchars(base64_encode($menu['id'])) ?>">Edit</a>
                                                        <form action="index.php?page=menu" method="POST">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="menu_id" value="<?= htmlspecialchars(base64_encode($menu['id'])) ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } else if (isset($_GET['page']) && $_GET['page'] === "createMenu") { ?>
                        <div class="card">
                            <div class="card-header">ADD MENU</div>
                            <div class="card-body">
                                <form action="index.php?page=menu" method="post">
                                    <div class="form-group">
                                        <label for="id_level">Level</label>
                                        <select name="id_level" id="id_level" class="form-control" required>
                                            <option value="">--Pilih Level--</option>
                                            <?php foreach ($levels as $level) { ?>
                                                <option value="<?= $level['id'] ?>"><?= $level['level_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="menu_name">Menu Name</label>
                                        <input type="text" name="menu_name" id="menu_name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="url">URL Name</label>
                                        <input type="text" name="url" id="url" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="icon">Icons</label>
                                        <select name="icon" id="icon" class="form-control" required>
                                            <option value="">--Pilih Icons--</option>
                                            <option value="fas fa-fw fa-tachometer-alt">Tachometer <i class="fas fa-fw fa-tachometer-alt"></i></option>
                                            <option value="fas fa-fw fa-cog">Cog <i class="fas fa-fw fa-cog"></i></option>
                                            <option value="fas fa-fw fa-wrench">Wrench <i class="fas fa-fw fa-wrench"></i></option>
                                            <option value="fas fa-fw fa-folder">Folder <i class="fas fa-fw fa-folder"></i></option>
                                            <option value="fas fa-fw fa-chart-area">Chart Area <i class="fas fa-fw fa-chart-area"></i></option>
                                            <option value="fas fa-fw fa-table">Table <i class="fas fa-fw fa-table"></i></option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="make_table">Make Table</label>
                                        <select name="make_table" id="make_table" class="form-control" required>
                                            <option value="">--Make Table?--</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="num_columns_div" style="display: none;">
                                        <label for="num_columns">Number of Columns</label>
                                        <input type="number" name="num_columns" id="num_columns" class="form-control" min="1">
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="store" value="Simpan">Submit</button>
                                    <a href="?page=menu" class="btn btn-danger">Back</a>
                                </form>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include '../inc/footer.php'; ?>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <?php include '../inc/scrolltop.php'; ?>

    <!-- Logout Modal-->
    <?php include '../inc/logout_modal.php'; ?>

    <!-- Bootstrap core JavaScript-->
    <?php include '../inc/js.php'; ?>

    <script>
        // Show/Hide num_columns_div based on make_table selection
        document.getElementById('make_table').addEventListener('change', function() {
            var makeTable = this.value;
            var numColumnsDiv = document.getElementById('num_columns_div');
            if (makeTable === 'yes') {
                numColumnsDiv.style.display = 'block';
            } else {
                numColumnsDiv.style.display = 'none';
            }
        });
    </script>

</body>

</html>
