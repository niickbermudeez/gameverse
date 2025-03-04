<?php
session_start();
include 'config.php';

require './../vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $first_name       = $_POST['first_name'];
    $last_name        = $_POST['last_name'];
    $birth_date       = $_POST['birth_date']; 
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

    $sql = "INSERT INTO users (first_name, last_name, birth_date, country, email, username, password, active, activationCode)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        header("Location: ./../web/register.html?error=Database error");
        exit();
    }

    $stmt->bind_param("sssssssis", $first_name, $last_name, $birth_date, $country, $email, $username, $hashed_password, $active, $activationCode);

    if ($stmt->execute()) {
        $activationUrl = "http://localhost/gameverse/php/mailCheckAccount.php?code=$activationCode&mail=$email";

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
            $mail->addAddress($email, $first_name);

            $mail->isHTML(true);
            $mail->Subject = '✅ Verify Your Email - Gameverse';
            $mail->Body    = "
                <div style='background-color: #0D0D2B; padding: 20px; text-align: center; color: #E5E5E5; font-family: Arial, sans-serif;'>
                    <h1>Verify Your Email ✅</h1>
                    <p>Hey <strong>$username</strong>,</p>
                    <p>Thank you for signing up at Gameverse! Please verify your email by clicking the button below.</p>
                    <a href='$activationUrl' style='display: inline-block; padding: 15px 25px; background: #F72585; color: #fff; text-decoration: none; border-radius: 5px;'>✅ Verify Your Email</a>
                    <p>If you didn't sign up for Gameverse, please ignore this email.</p>
                </div>
            ";

            $mail->send();
            header("Location: ./../php/login.php?success=Registered successfully! Please check your email.");
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
