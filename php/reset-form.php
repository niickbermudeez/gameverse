<?php
require './config.php';

if (!isset($_GET['code']) || !isset($_GET['email'])) {
    die("Invalid request.");
}

$code = $_GET['code'];
$email = urldecode($_GET['email']);

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - Gameverse</title>
    <link rel="stylesheet" href="./../css/new_password.css">
</head>
<body>
    <div class="password-reset-container">
        <h1>Set a New Password</h1>
        <form class="password-reset-form" action="./update-password.php" method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="code" value="<?php echo htmlspecialchars($code); ?>">

            <div class="input-group">
                <label for="password">New Password:</label>
                <input type="password" name="password" class="password-input" required>
            </div>

            <button type="submit">Update Password</button>
        </form>
    </div>
</body>
</html>
