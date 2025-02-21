<?php
require './config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $code = $_POST["code"];
    $newPassword = password_hash($_POST["password"], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND resetPassCode = ? AND resetPassExpiry > NOW()");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        die("Invalid or expired reset link.");
    }

    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE users SET password = ?, resetPassCode = NULL, resetPassExpiry = NULL WHERE id = ?");
    $stmt->bind_param("si", $newPassword, $user_id);

    if ($stmt->execute()) {
        echo "Your password has been updated. <a href='./../php/login.php'>Login here</a>";
    } else {
        echo "Error updating password.";
    }

    $stmt->close();
    $conn->close();
}
?>
