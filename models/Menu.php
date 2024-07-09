<?php

// Function to get menu data by ID
function getMenuById($db, $id_menu) {
    $id_menu = mysqli_real_escape_string($db, $id_menu);
    $query = mysqli_query($db, "SELECT * FROM menus WHERE id = $id_menu");
    return mysqli_fetch_assoc($query);
}

?>