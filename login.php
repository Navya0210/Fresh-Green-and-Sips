<?php
session_start();
include 'db.php';  // Include the connection file

// Get form data
$username = $_POST['username'];
$password = $_POST['password'];

// Get user from database
$stmt = $conn->prepare("SELECT id, password_hash FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($user_id, $password_hash);
    $stmt->fetch();

    // Verify password
    if (password_verify($password, $password_hash)) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        header("Location: menu.html");
        exit();
    } else {
        echo "Incorrect password. Please <a href='login.html'>try again</a>.";
    }
} else {
    echo "Username not found. Please <a href='Register.html'>register</a>.";
}

$stmt->close();
$conn->close();
?>
