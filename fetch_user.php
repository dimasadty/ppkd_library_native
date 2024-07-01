<?php
include 'config/config.php'; // Adjust the path as necessary

// Check if the user is logged in by verifying the session variable
if (isset($_SESSION['users_id'])) {
    $usersId = $_SESSION['users_id'];

    // Prepare the SQL query to fetch the user's name and email
    $sql = "SELECT name, email FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    // Check if the statement was prepared successfully
    if ($stmt) {
        $stmt->bind_param("i", $usersId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the user was found in the database
        if ($result->num_rows > 0) {
            $dataUsers = $result->fetch_assoc();
        } else {
            // Handle the case where the user is not found
            $dataUsers = ['name' => 'User Not Found', 'email' => ''];
        }

        // Close the statement
        $stmt->close();
    } else {
        // Handle the case where the SQL statement could not be prepared
        $dataUsers = ['name' => 'Error', 'email' => ''];
    }
} else {
    // Handle the case where the user is not logged in
    $dataUsers = ['name' => 'Guest', 'email' => ''];
}
?>