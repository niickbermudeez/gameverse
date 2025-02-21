<?php
session_start();
include 'config.php';

require './../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $country = $_POST['country'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

<<<<<<< HEAD
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $activationCode  = hash("sha256", random_bytes(32));
        $active          = 0;

        $sql = "INSERT INTO users (first_name, last_name, age, country, email, username, password, active, activationCode)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $error = "Database error";
        } else {
            $stmt->bind_param("ssissssis", $first_name, $last_name, $age, $country, $email, $username, $hashed_password, $active, $activationCode);

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
                    $success = "Registered successfully! Please check your email.";
                } catch (Exception $e) {
                    $error = "Mail error: " . $mail->ErrorInfo;
                }
            } else {
                $error = "Registration failed. Try again.";
            }

            $stmt->close();
        }
=======
    if ($password !== $confirm_password) 
    {
        header("Location: ./../web/register.html?error=" . urlencode("Passwords do not match."));
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $activationCode = hash("sha256", random_bytes(32)); 
    $active = 0;

    $sql = "INSERT INTO users (first_name, last_name, age, country, email, username, password, active, activationCode) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) 
    {
        header("Location: ./../web/register.html?error=Database error");
        exit();
    }

    $stmt->bind_param("ssissssis", $first_name, $last_name, $age, $country, $email, $username, $hashed_password, $active, $activationCode);

    if ($stmt->execute()) 
    {
        $activationUrl = "http://localhost/gameverse/php/mailCheckAccount.php?code=$activationCode&mail=$email";

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        try 
        {
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
            $mail->Subject = '🔑 Reset Your Password - Gameverse';
            $mail->Body = "
            <div style='background-color: #0D0D2B; padding: 20px; text-align: center; color: #E5E5E5; font-family: Arial, sans-serif;'>
            <div style='max-width: 600px; margin: auto; background-color: #1B1E56; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);'>
            <h1 style='background: linear-gradient(90deg, #F72585, #4361EE); -webkit-background-clip: text; -webkit-text-fill-color: transparent;'>
                Reset Your Password 🔑
            </h1>
            <p style='font-size: 16px;'>Hey <strong>$username</strong>,</p>
            <p style='font-size: 16px; line-height: 1.5;'>We received a request to reset your password for your Gameverse account. If this was you, click the button below to set a new password.</p>
            <a href='$resetLink' 
               style='display: inline-block; margin: 20px auto; padding: 15px 25px; font-size: 18px; color: #fff; background: linear-gradient(90deg, #F72585, #4361EE); 
               text-decoration: none; border-radius: 5px; font-weight: bold;'>
               🔒 Reset Your Password
            </a>
            <p style='font-size: 14px; margin-top: 20px;'>If you didn't request this, please ignore this email. This link will expire in 1 hour.</p>
            <hr style='border: 1px solid #4361EE; margin: 20px 0;'>
            <p style='font-size: 12px; color: #aaa;'>Gameverse &copy; " . date('Y') . ". All Rights Reserved.</p>
        </div>
    </div>
";

            

            $mail->send();
            header("Location: ./../web/login.html?success=Registered successfully! Please check your email.");
            exit();
        } catch (Exception $e) 
        {
            header("Location: ./../web/register.html?error=Mail error: {$mail->ErrorInfo}");
            exit();
        }
    } else 
    {
        header("Location: ./../web/register.html?error=Registration failed. Try again.");
        exit();
>>>>>>> parent of 32a34c3 (funcionaPerfectamente)
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Gameverse</title>
    <link rel="stylesheet" href="./../css/register.css">
    <link rel="shortcut icon" href="./../img/GV.png" type="image/x-icon">
</head>
<body>
    <main>
        <div class="logo">
            <img src="./../img/logo.png" alt="logo">
        </div>
        <section class="register-form-container">
            <h1>Create Your Account</h1>
            <?php 
                if (isset($error)) { echo "<p class='error-message'>$error</p>"; } 
                if (isset($success)) { echo "<p class='success-message'>$success</p>"; }
            ?>
            <form action="register.php" method="POST" class="register-form">
                <div class="input-group">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" name="first_name" placeholder="Enter your First Name" required>
                </div>
                <div class="input-group">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" name="last_name" placeholder="Enter your Last Name" required>
                </div>
                <div class="input-group">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" placeholder="Enter your age" required>
                </div>
                <div class="input-group">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" placeholder="Enter your country" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your Email" required>
                </div>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a Username" required>
                </div>
                <div class="input-group password-container">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter a Password" required>
                    <span id="toggle-password" class="eye-button">👁️</span>
                </div>
                <div class="input-group password-container">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Re-enter your Password" required>
                    <span id="toggle-confirm-password" class="eye-button">👁️</span>
                </div>
                <button type="submit">Register</button>
            </form>
        </section>
    </main>

    <script src="./../js/register.js"></script>
    <script src="./../js/reset_form.js"></script>
</body>
</html>
