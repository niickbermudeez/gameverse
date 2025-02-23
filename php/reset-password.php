<?php
session_start();

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require './config.php';
require './../vendor/PHPMailer/PHPMailer/src/Exception.php';
require './../vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require './../vendor/PHPMailer/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        $_SESSION['message_type'] = "error";
        header("Location: reset-password.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $_SESSION['message'] = "No account found with that email.";
        $_SESSION['message_type'] = "error";
        header("Location: reset-password.php");
        exit();
    }

    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    $resetCode  = bin2hex(random_bytes(32));
    $expiryTime = date("Y-m-d H:i:s", strtotime("+1 hour"));

    $stmt = $conn->prepare("UPDATE users SET resetPassCode = ?, resetPassExpiry = ? WHERE id = ?");
    $stmt->bind_param("ssi", $resetCode, $expiryTime, $user_id);
    $stmt->execute();
    $stmt->close();

    $mail           = new PHPMailer(true);
    $mail->CharSet  = 'UTF-8';
    $mail->Encoding = 'base64';

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nick.bermudeze@educem.net';
        $mail->Password   = 'zqfx kfdq veiz hniy';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('nick.bermudeze@educem.net', 'Gameverse');
        $mail->addAddress($email);

        $resetLink = "http://localhost/gameverse/php/reset-form.php?code=$resetCode&email=" . urlencode($email);
        $mail->isHTML(true);
        $mail->Subject = '🔑 Reset Your Password - Gameverse';
        $mail->Body    = "
            <div style='background-color: #0D0D2B; padding: 20px; text-align: center; color: #E5E5E5; font-family: Arial, sans-serif;'>
                <div style='max-width: 600px; margin: auto; background-color: #1B1E56; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);'>
                    <h1 style='background: linear-gradient(90deg, #F72585, #4361EE); -webkit-background-clip: text; -webkit-text-fill-color: transparent;'>
                        Reset Your Password 🔑
                    </h1>
                    <p style='font-size: 16px; line-height: 1.5;'>It looks like you've requested to reset your password. Click the button below to proceed.</p>
                    <a href='$resetLink'
                       style='display: inline-block; margin: 20px auto; padding: 15px 25px; font-size: 18px; color: #fff; background: linear-gradient(90deg, #F72585, #4361EE);
                       text-decoration: none; border-radius: 5px; font-weight: bold;'>
                       🔒 Reset Password
                    </a>
                    <p style='font-size: 14px; margin-top: 20px;'>This link will expire in <b>1 hour</b>. If you didn’t request this, you can ignore this email.</p>
                    <hr style='border: 1px solid #4361EE; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #aaa;'>- The Gameverse Team</p>
                </div>
            </div>
        ";
        $mail->send();

        $_SESSION['message'] = "A reset link has been sent to your email.";
        $_SESSION['message_type'] = "success";
        header("Location: reset-password.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = "Error sending email: " . $mail->ErrorInfo;
        $_SESSION['message_type'] = "error";
        header("Location: reset-password.php");
        exit();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Gameverse</title>
    <link rel="stylesheet" href="./../css/reset_password.css">
    <link rel="shortcut icon" href="./../img/GV.png" type="image/x-icon">
</head>
<body>
    <main>
        <div class="logo">
            <img src="./../img/logo.png" alt="logo">
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert <?= ($_SESSION['message_type'] == 'success') ? 'alert-success' : 'alert-danger' ?>">
                <?= $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php endif; ?>

        <section class="reset-form-container">
            <h1>Reset Your Password</h1>
            <p>Enter your email to receive a password reset link.</p>
            <form action="reset-password.php" method="POST" class="reset-form">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your Email" required>
                </div>
                
                <button type="submit">Send Reset Link</button>
            </form>
            <p class="redirect">
                Remembered your password? <a href="./../php/login.php">Login here</a>
            </p>
        </section>
    </main>
</body>
</html>
