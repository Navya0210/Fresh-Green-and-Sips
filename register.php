<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "localhost";
$dbUsername = "root"; // XAMPP default MySQL username
$dbPassword = "";     // XAMPP default MySQL password (empty)
$dbname = "fresh_greens";

// Create database connection
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data safely
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement
    $sql = "INSERT INTO user1 (username, email, password, phone_number) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssss", $username, $email, $hashedPassword, $phone);

        if ($stmt->execute()) {
            echo "<h3>Registration successful!</h3>";
            echo "<a href='register.html'>Go back to form</a>";
        } else {
            echo "<h3>Error: " . $stmt->error . "</h3>";
        }

        $stmt->close();
    } else {
        echo "<h3>Database error: " . $conn->error . "</h3>";
    }
}

$conn->close();
?>

