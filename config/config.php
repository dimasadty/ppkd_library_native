<?php
$host_connection = "localhost";
$username_connection = "root";
$password_connection = "";
$database_connection = "db_library";

// Create connection
$db_library = new mysqli($host_connection, $username_connection, $password_connection, $database_connection);

// Check connection
if ($db_library->connect_error) {
    die("Connection failed: " . $db_library->connect_error);
}
?>