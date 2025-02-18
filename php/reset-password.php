<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './config.php';
require './../vendor/PHPMailer/PHPMailer/src/Exception.php';
require './../vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require './../vendor/PHPMailer/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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

    $resetCode = bin2hex(random_bytes(32));
    $expiryTime = date("Y-m-d H:i:s", strtotime("+1 hour"));

    $stmt = $conn->prepare("UPDATE users SET resetPassCode = ?, resetPassExpiry = ? WHERE id = ?");
    $stmt->bind_param("ssi", $resetCode, $expiryTime, $user_id);
    $stmt->execute();
    $stmt->close();

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'nick.bermudeze@educem.net';
        $mail->Password = 'zqfx kfdq veiz hniy'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('iker@chumeus.com', 'Gameverse');
        $mail->addAddress($email);

        $resetLink = "http://localhost/gameverse/php/reset-form.php?code=$resetCode&email=" . urlencode($email);
        $mail->Subject = "Reset Your Password - Gameverse";
        $mail->Body = "Click the link below to reset your password:\n\n$resetLink\n\nThis link expires in 1 hour.";
        $mail->send();

        echo "A password reset link has been sent to your email.";
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    $conn->close();
}
?>
