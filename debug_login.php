<?php
include 'includes/db.php';

$email = 'admin@vote.com';
$password = 'admin123';

echo "<h2>Admin Login Debugger</h2>";

// 1. Check if user exists
$stmt = $conn->prepare("SELECT id, password_hash FROM admins WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<h3 style='color:red'>Admin user '$email' NOT FOUND in database!</h3>");
}

$row = $result->fetch_assoc();
$stored_hash = $row['password_hash'];

echo "<p><strong>User Found:</strong> Yes</p>";
echo "<p><strong>Stored Hash:</strong> " . htmlspecialchars($stored_hash) . "</p>";
echo "<p><strong>Hash Length:</strong> " . strlen($stored_hash) . "</p>";

// 2. Verify Password
if (password_verify($password, $stored_hash)) {
    echo "<h3 style='color:green'>SUCCESS: Password 'admin123' matches the stored hash.</h3>";
    echo "<p>Login should work. Please make sure you are selecting <strong>'Admin'</strong> in the Role dropdown.</p>";
} else {
    echo "<h3 style='color:red'>FAILURE: Password mismatch.</h3>";
    echo "<p>Attempting to fix...</p>";
    
    // 3. Fix it automatically
    $new_hash = password_hash($password, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE admins SET password_hash = ? WHERE email = ?");
    $update->bind_param("ss", $new_hash, $email);
    if ($update->execute()) {
        echo "<p style='color:blue'>Password has been reset to 'admin123'. <strong>Try logging in now.</strong></p>";
        echo "<p>New Hash: $new_hash</p>";
    } else {
        echo "<p style='color:red'>Could not update password: " . $conn->error . "</p>";
    }
}
?>
