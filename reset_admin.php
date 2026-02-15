<?php
include 'includes/db.php';

$email = 'admin@vote.com';
$password = 'admin1234';
$new_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE admins SET password_hash = ? WHERE email = ?");
$stmt->bind_param("ss", $new_hash, $email);

if ($stmt->execute()) {
    echo "<h1>Admin password reset successfully</h1>";
    echo "<p>Email: $email</p>";
    echo "<p>Password: $password</p>";
    echo "<p>New Hash: $new_hash</p>";
    echo "<br><a href='Login/login.php'>Go to Login</a>";
} else {
    echo "Error resetting password: " . $conn->error;
}
?>
