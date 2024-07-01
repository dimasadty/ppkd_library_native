<?php
try{
    $dsn = "mysql:host=localhost;dbname=db_latihanujikom";
    $username = "root";
    $password = "";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];
    $db = new PDO($dsn, $username, $password, $options);
} catch (PDOException $th) {
    die("Connection Failed to Database" . $th->getMessage());

}