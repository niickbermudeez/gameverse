<?php
require './config.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$message = "";

if (!isset($_GET['code']) || !isset($_GET['email'])) {
    $message = "Invalid request.";
} else {
    $email = urldecode($_GET['email']);
    $code = $_GET['code'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND resetPassCode = ? AND resetPassExpiry > NOW()");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $message = "Invalid or expired reset link.";
    } else {
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newPassword = password_hash($_POST["password"], PASSWORD_BCRYPT);

            $stmt = $conn->prepare("UPDATE users SET password = ?, resetPassCode = NULL, resetPassExpiry = NULL WHERE id = ?");
            $stmt->bind_param("si", $newPassword, $user_id);

            if ($stmt->execute()) {
                $message = "Your password has been updated. <a href='./../php/login.php'>Login here</a>";
            } else {
                $message = "Error updating password.";
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - Gameverse</title>
    <link rel="stylesheet" href="./../css/new-password.css">
</head>
<body>
    <div class="password-reset-container">
        <h1>Set a New Password</h1>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php elseif (empty($message) && !isset($_POST['password'])): ?>
            <form class="password-reset-form" action="" method="POST">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="code" value="<?php echo htmlspecialchars($code); ?>">

                <div class="input-group password-container">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter a Password" required>
                    <span id="toggle-password" class="eye-button">👁️</span>
                    <div id="password-error" class="error-message"></div>
                </div>
                <div class="input-group password-container">
                    <label for="confirm-password">Confirm New Password</label>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Re-enter your Password" required>
                    <span id="toggle-confirm-password" class="eye-button">👁️</span>
                    <div id="confirm-password-error" class="error-message"></div>
                </div>

                <button type="submit">Update Password</button>
            </form>
        <?php endif; ?>
    </div>
    <script src="./../js/reset_form.js"></script>

</body>
</html>
