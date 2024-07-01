<?php
require_once 'config/config.php'; // Ensure this path is correct
require_once 'com/function.php'; // Corrected path to function.php

function query($query)
{
    global $db_library;
    $mysqli = $db_library;
    $result = mysqli_query($mysqli, $query);
    if (!$result) {
        die("Query failed: ". mysqli_error($mysqli));
    }
    $rows = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_free_result($result);
    return $rows;
}
?>