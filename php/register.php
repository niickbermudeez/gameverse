<?php
session_start();
include 'config.php';

require './../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $country = $_POST['country'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        header("Location: ./../web/register.html?error=" . urlencode("Passwords do not match."));
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $activationCode = hash("sha256", random_bytes(32)); 
    $active = 0;

    $sql = "INSERT INTO users (first_name, last_name, age, country, email, username, password, active, activationCode) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        header("Location: ./../web/register.html?error=Database error");
        exit();
    }

    $stmt->bind_param("ssissssis", $first_name, $last_name, $age, $country, $email, $username, $hashed_password, $active, $activationCode);

    if ($stmt->execute()) {
        $activationUrl = "http://localhost/gameverse/php/mailCheckAccount.php?code=$activationCode&mail=$email";

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'nick.bermudeze@educem.net'; 
            $mail->Password = 'zqfx kfdq veiz hniy';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('nick.bermudeze@educem.net', 'Gameverse');
            $mail->addAddress($email, $first_name);

            $mail->isHTML(true);
            $mail->Subject = 'Welcome to Gameverse! Activate your account';
            $mail->Body = "
                <h1>Welcome to Gameverse!</h1>
                <p>Thank you for registering. Please click the link below to activate your account:</p>
                <a href='$activationUrl'>Activate your account Now!</a>
            ";

            $mail->send();
            header("Location: ./../web/login.html?success=Registered successfully! Please check your email.");
            exit();
        } catch (Exception $e) {
            header("Location: ./../web/register.html?error=Mail error: {$mail->ErrorInfo}");
            exit();
        }
    } else {
        header("Location: ./../web/register.html?error=Registration failed. Try again.");
        exit();
    }

    $stmt->close();
    $conn->close();
}
