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

// Second query
$query2 = "SELECT b.*, m.name, db.booksdate_in, db.booksdate_out 
           FROM borrowers b 
           LEFT JOIN members m ON m.id = b.id_member 
           LEFT JOIN detailed_borrowers db ON db.id_borrower = b.id 
           ORDER BY b.id DESC";
$result2 = $db_library->query($query2);
if ($result2) {
    while ($row = $result2->fetch_assoc()) {
        $borrowers_details[] = $row;
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
    <title>Library PPKD Jakarta Pusat - Detailed</title>
    <!-- plugins:css -->
    <?php include '../inc/css.php'; ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
            text-align: left;
        }

        td {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
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
        <div class="container">
            <h2>Library PPKD Jakarta Pusat</h2>
            <table border="1" style="width: 100%">
                <tr>
                    <th width="1%">No</th>
                    <th>No Transaksi</th>
                    <th>Borrowers</th>
                    <th>Date Out</th>
                    <th>Date In</th>
                </tr>
                <?php
                $no = 1;
                foreach ($borrowers as $databorrowers) { 
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $databorrowers['no_transaction']; ?></td>
                        <td><?php echo $databorrowers['name']; ?></td>
                        <td><?php echo $databorrowers['booksdate_out']; ?></td>
                        <td><?php echo $databorrowers['booksdate_in']; ?></td>
                    </tr>
                <?php  }?>
            </table>
            <div>
                <p>Diterima Oleh:</p>
                <p><u>(Nama Penerima)</u></p>
            </div>
            <div style="margin-top: 20px;">
                <p>TTD:</p>
                <p><u>(Tanda Tangan)</u></p>
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