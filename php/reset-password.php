<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require './config.php';
require './../vendor/PHPMailer/PHPMailer/src/Exception.php';
require './../vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require './../vendor/PHPMailer/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        echo "No account found with that email.";
        exit;
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

        echo "A password reset link has been sent to your email.";
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    $conn->close();
}
