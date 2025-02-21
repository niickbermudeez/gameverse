<?php
session_start();
include 'config.php';

require './../vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name       = $_POST['first_name'];
    $last_name        = $_POST['last_name'];
    $age              = $_POST['age'];
    $country          = $_POST['country'];
    $email            = $_POST['email'];
    $username         = $_POST['username'];
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        header("Location: ./../web/register.html?error=" . urlencode("Passwords do not match."));
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $activationCode  = hash("sha256", random_bytes(32));
    $active          = 0;

    $sql = "INSERT INTO users (first_name, last_name, age, country, email, username, password, active, activationCode)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (! $stmt) {
        header("Location: ./../web/register.html?error=Database error");
        exit();
    }

    $stmt->bind_param("ssissssis", $first_name, $last_name, $age, $country, $email, $username, $hashed_password, $active, $activationCode);

    if ($stmt->execute()) {
        $activationUrl = "http://localhost/gameverse/php/mailCheckAccount.php?code=$activationCode&mail=$email";

        $mail           = new PHPMailer(true);
        $mail->CharSet  = 'UTF-8';
        $mail->Encoding = 'base64';

        try
        {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'nick.bermudeze@educem.net';
            $mail->Password   = 'zqfx kfdq veiz hniy';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('nick.bermudeze@educem.net', 'Gameverse');
            $mail->addAddress($email, $first_name);

            $mail->isHTML(true);
            $mail->Subject = '✅ Verify Your Email - Gameverse';
            $mail->Body    = "
    <div style='background-color: #0D0D2B; padding: 20px; text-align: center; color: #E5E5E5; font-family: Arial, sans-serif;'>
        <div style='max-width: 600px; margin: auto; background-color: #1B1E56; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);'>
            <h1 style='background: linear-gradient(90deg, #F72585, #4361EE); -webkit-background-clip: text; -webkit-text-fill-color: transparent;'>
                Verify Your Email ✅
            </h1>
            <p style='font-size: 16px;'>Hey <strong>$username</strong>,</p>
            <p style='font-size: 16px; line-height: 1.5;'>Thank you for signing up at Gameverse! Please verify your email address by clicking the button below.</p>
            <a href='$activationUrl'
               style='display: inline-block; margin: 20px auto; padding: 15px 25px; font-size: 18px; color: #fff; background: linear-gradient(90deg, #F72585, #4361EE);
               text-decoration: none; border-radius: 5px; font-weight: bold;'>
               ✅ Verify Your Email
            </a>
            <p style='font-size: 14px; margin-top: 20px;'>If you didn't sign up for Gameverse, please ignore this email.</p>
            <hr style='border: 1px solid #4361EE; margin: 20px 0;'>
            <p style='font-size: 12px; color: #aaa;'>Gameverse &copy; " . date('Y') . ". All Rights Reserved.</p>
        </div>
    </div>
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