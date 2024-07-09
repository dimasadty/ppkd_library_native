<?php
// Include config.php to get $db_library connection details
include '../config/config.php';

// Function to get menu data by ID (you may need to define this function)
function getMenuById($db, $id) {
    $query = mysqli_query($db, "SELECT * FROM menus WHERE id = '$id'");
    return mysqli_fetch_assoc($query);
}

// Handle form submissions or other menu-related actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Insert or update operation
    if (isset($_POST['store']) && $_POST['store'] === "Simpan") {
        $id_level = htmlspecialchars($_POST['id_level']);
        $menu_name = htmlspecialchars($_POST['menu_name']);
        $url = htmlspecialchars($_POST['url']);
        $icon = htmlspecialchars($_POST['icon']);
        $num_columns = htmlspecialchars($_POST['num_columns']);
        $make_table = htmlspecialchars($_POST['make_table']);

        // Validate id_level against levels table
        $checkLevelQuery = mysqli_prepare($db_library, "SELECT COUNT(*) as count FROM levels WHERE id = ?");
        mysqli_stmt_bind_param($checkLevelQuery, "i", $id_level);
        mysqli_stmt_execute($checkLevelQuery);
        $levelCountResult = mysqli_stmt_get_result($checkLevelQuery);
        $levelCount = mysqli_fetch_assoc($levelCountResult)['count'];

        if ($levelCount > 0) {
            // Insert into `menus` table
            $insertmenus = mysqli_prepare($db_library, "INSERT INTO menus (id_level, menu_name, url, icon, num_columns, make_table, created_at, updated_at) 
                                                VALUES (?, ?, ?, ?, ?, ?, now(), now())");
            mysqli_stmt_bind_param($insertmenus, "isssis", $id_level, $menu_name, $url, $icon, $num_columns, $make_table);
            $insertResult = mysqli_stmt_execute($insertmenus);

            // Handle result of insert query
            if ($insertResult) {
                $id_menu = mysqli_insert_id($db_library);

                // Insert into `menu_column` if make_table is 'yes'
                if ($make_table == 'yes') {
                    for ($i = 1; $i <= $num_columns; $i++) {
                        $column_name = 'column' . $i;
                        if (isset($_POST[$column_name])) {
                            $column_name_value = htmlspecialchars($_POST[$column_name]);
                            $insertColumn = mysqli_prepare($db_library, "INSERT INTO menu_column (id_menu, column_name, created_at, updated_at) 
                                                                VALUES (?, ?, now(), now())");
                            mysqli_stmt_bind_param($insertColumn, "is", $id_menu, $column_name_value);
                            $insertColumnResult = mysqli_stmt_execute($insertColumn);
                            if (!$insertColumnResult) {
                                die("Error inserting data into menu_column: " . mysqli_error($db_library));
                            }
                        }
                    }
                }

                // Redirect after successful operation
                header("location: ../pagesmenus/index.php?page=menu&store=success");
                exit;
            } else {
                die("Error inserting data into menu: " . mysqli_error($db_library));
            }
        } else {
            die("Invalid id_level: $id_level");
        }
    } elseif (isset($_POST['update']) && $_POST['update'] === "Edit") {
        // Handle menu update
        $id_menu = htmlspecialchars($_POST['menuIdEdt']);
        $menusData = getMenuById($db_library, $id_menu);

        if ($menusData) {
            $id_level = htmlspecialchars($_POST['id_level']);
            $menu_name = htmlspecialchars($_POST['menu_name']);
            $url = htmlspecialchars($_POST['url']);
            $icon = htmlspecialchars($_POST['icon']);
            $make_table = htmlspecialchars($_POST['make_table']);
            $num_columns = (int)$_POST['num_columns'];

            // Update `menus` table
            $updateMenu = mysqli_prepare($db_library, "UPDATE menus SET id_level = ?, menu_name = ?, url = ?, icon = ?, 
                                                 make_table = ?, num_columns = ?, updated_at = now() WHERE id = ?");
            mysqli_stmt_bind_param($updateMenu, "isssisi", $id_level, $menu_name, $url, $icon, $make_table, $num_columns, $id_menu);
            $updateResult = mysqli_stmt_execute($updateMenu);

            if ($updateResult) {
                // Handle updating or deleting menu columns as needed
                // For simplicity, assuming you handle column updates/deletions similarly as in insertion

                // Redirect after successful operation
                header("location: ../pagesmenus/index.php?page=menu");
                exit;
            } else {
                die("Error updating menu: " . mysqli_error($db_library));
            }
        } else {
            die("Menu not found.");
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        // Handle menu deletion
        if (isset($_POST['menu_id'])) {
            $menu_id = htmlspecialchars(base64_decode($_POST['menu_id']));

            // Delete menu from `menus` table
            $deleteMenu = mysqli_prepare($db_library, "DELETE FROM menus WHERE id = ?");
            mysqli_stmt_bind_param($deleteMenu, "i", $menu_id);
            $deleteMenuResult = mysqli_stmt_execute($deleteMenu);

            // Delete associated menu columns from `menu_column` table
            $deleteMenuColumns = mysqli_prepare($db_library, "DELETE FROM menu_column WHERE id_menu = ?");
            mysqli_stmt_bind_param($deleteMenuColumns, "i", $menu_id);
            $deleteColumnsResult = mysqli_stmt_execute($deleteMenuColumns);

            // Redirect after deletion
            header('Location: ../pagesmenus/index.php?page=menu');
            exit;
        }
    }
}

// Close database connection at the end of your script
mysqli_close($db_library);
?>
