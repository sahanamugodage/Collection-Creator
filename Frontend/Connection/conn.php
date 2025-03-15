<?php
// Define the Host & user Details
$host_name = "localhost";
$user_name = "root";
$user_password = "root";

// Build Connection
$conn = new mysqli($host_name,$user_name,$user_password);

// Define connection errors
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>