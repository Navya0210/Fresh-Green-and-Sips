<?php
$servername = "localhost";
$db_username = "root";
$db_password = "Ammulu@3015"; // replace with your DB password if set
$dbname = "fresh_greens";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
