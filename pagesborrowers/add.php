<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    header("location:/library/login.php?error-access-failed");
    exit; // Ensure to exit after redirection
}

// Include configuration file and fetch_user.php
include '../config/config.php';
include('../fetch_user.php');

// Example query with MySQLi
$query = "SELECT borrowers.*, members.name FROM borrowers LEFT JOIN members ON members.id = borrowers.id_member ORDER BY borrowers.id DESC";
$result = $db_library->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $borrowers[] = $row;
    }
} else {
    echo "Query failed: " . $db_library->error;
}

// Fetch the maximum id from borrowers table using MySQLi prepared statement
$maxIdQuery = "SELECT MAX(id) AS max_id FROM borrowers";
$stmt = $db_library->prepare($maxIdQuery);
$stmt->execute();
$maxIdResult = $stmt->get_result();

if ($maxIdResult) {
    $data = $maxIdResult->fetch_assoc();
    $maxId = $data['max_id']; // Fetching the max id
    $sort = $maxId + 1;

    // Constructing the transaction number with format TRddmmyyyyxxx
    $no_transaction = "TR" . date("dmY") . sprintf("%03s", $sort);
} else {
    echo "Query failed: " . $db_library->error;
}

// If the form is submitted, handle the form data
if (isset($_POST['submit'])) {
    $id_member = $_POST['id_member'];
    $no_transaction = $_POST['no_transaction'];
    $booksname = $_POST['booksname'];
    $booksdate_out = $_POST['booksdate_out'];
    $booksdate_in = $_POST['booksdate_in'];

    // Check if id_member is empty
    if (empty($id_member)) {
        echo '<script>alert("Please select a member.");</script>';
    } else {
        try {
            // Start transaction
            $db_library->autocommit(false);

            // Insert into borrowers table
            $insertborrowers = $db_library->prepare("INSERT INTO borrowers (id_member, no_transaction) VALUES (?,?)");
            $insertborrowers->bind_param("is", $id_member, $no_transaction);
            $insertborrowers->execute();

            // Get the last inserted ID
            $lastInsertId = $db_library->insert_id;

            // Insert into detailed_borrowers table for each row
            $insertdetailed_borrowers = $db_library->prepare("INSERT INTO detailed_borrowers (id_borrower, id_books, booksdate_out, booksdate_in, description) VALUES (?,?,?,?,?)");
            for ($i = 0; $i < count($booksname); $i++) {
                // Check if the necessary keys are set before trying to access them
                if (isset($_POST['booksdate_in'][$i]) && isset($_POST['description'][$i])) {
                    $booksdate_in = $_POST['booksdate_in'][$i];
                    $description = $_POST['description'][$i];
                } else {
                    // Handle the case where the necessary keys are not set
                    echo "Error: Missing necessary data.";
                    exit;
                }

                $insertdetailed_borrowers->bind_param("iisss", $lastInsertId, $booksname[$i], $booksdate_out[$i], $booksdate_in, $description);
                $insertdetailed_borrowers->execute();
            }

            // Commit transaction
            $db_library->commit();

            header("location:/library/pagesborrowers/index.php?notif=success");
            exit;
        } catch (Exception $e) {
            // Rollback transaction on error
            $db_library->rollback();
            echo "Error: " . $e->getMessage();
        } finally {
            $db_library->autocommit(true);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Library PPKD Jakarta Pusat - Borrowers</title>
    <!-- CSS -->
    <?php include '../inc/css.php'; ?>
    <style>
        .btn-new-member {
            margin-top: 38px;
            /* Adjust this value to align perfectly */
        }
    </style>
</head>

<body class="with-welcome-text">
    <!-- Navbar -->
    <?php include '../inc/navbar.php'; ?>

    <div class="container-fluid page-body-wrapper">
        <!-- Sidebar -->
        <?php include '../inc/sidebar.php'; ?>

        <!-- Main content -->
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h5><b>Add Borrowers - Library PPKD Jakarta Pusat</b></h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST" id="borrowerForm">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="id_member" class="form-label">Select Member:</label>
                                <select class="form-select form-select-lg" name="id_member" id="id_member">
                                    <option value="">--Select Member--</option>
                                    <?php
                                    $querymembers = $db_library->query("SELECT * FROM members");
                                    while ($datamembers = $querymembers->fetch_assoc()) {
                                        echo '<option value="' . $datamembers['id'] . '">' . $datamembers['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <a href="/library/pagesmembers/add.php" class="btn btn-success btn-sm btn-new-member">
                                    <h6>New Member</h6>
                                </a>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <label for="no_transaction" class="form-label">No Transaction:</label>
                                <input readonly name="no_transaction" value="<?php echo $no_transaction ?>" type="text" class="form-control form-control-lg" id="no_transaction" placeholder="No Transaction...">
                            </div>
                        </div>

                        <!-- Tombol untuk menambah baris input -->
                        <div align="right" class="mb-3">
                            <button type="button" class="btn btn-primary" id="addRow">Add More</button>
                        </div>

                        <!-- Tabel untuk menampilkan data peminjam -->
                        <div class="table-responsive">
                            <table class="table table-bordered" id="borrowersTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Books Name</th>
                                        <th>Books Out</th>
                                        <th>Books In</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Baris input dinamis akan ditambahkan di sini menggunakan JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <!-- Tombol submit -->
                        <div class="mt-3 d-grid gap-2">
                            <button name="submit" type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <?php include '../inc/js.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var addButton = document.getElementById('addRow');
            var tableBody = document.querySelector('#borrowersTable tbody');
            var rowCount = 1;

            addButton.addEventListener('click', function() {
                var row = `
                <tr>
                    <td>${rowCount}</td>
                    <td>
                        <select class="form-select form-select-lg" name="booksname[]" class="form-control"><b>
                            <option value="">Select Books</option>
                            <?php
                            $querybooks = $db_library->query("SELECT * FROM books");
                            while ($databooks = $querybooks->fetch_assoc()) {
                                echo '<option value="' . $databooks['id'] . '">' . $databooks['booksname'] . '</option>';
                            }
                            ?>
                            </b>
                        </select>
                    </td>
                    <td><input type="date" class="form-control" name="booksdate_out[]" placeholder="Books Out..."></td>
                    <td><input type="date" class="form-control" name="booksdate_in[]" placeholder="Books In..."></td>
                    <td><input type="text" class="form-control" name="description[]" placeholder="Description..."></td>
                    <td><a href="#" class="btn btn-danger btn-sm delete-row">Delete</a></td>
                </tr>
            `;
                tableBody.insertAdjacentHTML('beforeend', row);
                rowCount++;
            });

            // Menghapus baris input
            tableBody.addEventListener('click', function(e) {
                if (e.target.classList.contains('delete-row')) {
                    e.preventDefault();
                    var currentRow = e.target.closest('tr');
                    currentRow.remove();
                    // Update row numbers after deletion
                    updateRowNumbers();
                }
            });

            // Function to update row numbers
            function updateRowNumbers() {
                var rows = tableBody.querySelectorAll('tr');
                rowCount = 1; // Reset rowCount
                rows.forEach(function(row) {
                    row.querySelector('td:first-child').textContent = rowCount;
                    rowCount++;
                });
            }
        });
    </script>
</body>

</html>