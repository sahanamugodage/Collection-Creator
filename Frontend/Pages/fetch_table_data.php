<?php
session_start(); // Start session to access stored data

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Auth/Login.php"); // Redirect if not logged in
    exit();
}
include "../Connection/conn.php"; 

if (!isset($_SESSION['database_name']) || !isset($_GET['table'])) {
    die(json_encode(["error" => "Invalid request"]));
}

$selected_database = $_SESSION['database_name'];
$table_name = $_GET['table'];

// Fetch column names
$column_query = "DESCRIBE `$selected_database`.`$table_name`";
$columns_result = $conn->query($column_query);

if (!$columns_result) {
    die(json_encode(["error" => "Query failed to fetch column names"]));
}

$columns = [];
while ($column = $columns_result->fetch_assoc()) {
    $columns[] = $column['Field'];  // Fetch the column name
}

// Fetch table data
$query = "SELECT * FROM `$selected_database`.`$table_name` LIMIT 10"; // Fetch first 10 rows
$result = $conn->query($query);

if (!$result) {
    die(json_encode(["error" => "Query failed"]));
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Return both column names and data
echo json_encode(['columns' => $columns, 'data' => $data]);
?>