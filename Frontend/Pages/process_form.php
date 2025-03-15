<?php
session_start(); // Start session to access stored data

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Auth/Login.php"); // Redirect if not logged in
    exit();
}
include "../Connection/conn.php";

if (!isset($_SESSION['database_name'])) {
    die("Error: No database selected.");
}

$selected_database = $_SESSION['database_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table_name = $_GET['table_name']; // Get the table name from the URL

    // Build the SQL query dynamically
    $columns = [];
    $values = [];

    // Loop through POST data to construct the columns and values
    foreach ($_POST as $column_name => $value) {
        $columns[] = "`$column_name`";
        $values[] = "'" . $conn->real_escape_string($value) . "'";
    }

    $columns_sql = implode(", ", $columns);
    $values_sql = implode(", ", $values);

    $insert_query = "INSERT INTO `$selected_database`.`$table_name` ($columns_sql) VALUES ($values_sql)";
    
    if ($conn->query($insert_query)) {
        echo "Record inserted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>