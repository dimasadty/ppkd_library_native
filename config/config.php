<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "db_library";

// Create MySQLi connection
$db_library = new mysqli($host, $username, $password, $database);

// Check connection
if ($db_library->connect_error) {
    die("Connection failed: " . $db_library->connect_error);
}
?>