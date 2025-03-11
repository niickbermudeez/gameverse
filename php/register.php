<?php
session_start();
include 'config.php';

require './../vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$error_message = "";
$success_message = "";

$sql = "SELECT id, name FROM countries ORDER BY name ASC";
$result = $conn->query($sql);
$countries = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $countries[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $first_name       = $_POST['first_name'];
    $last_name        = $_POST['last_name'];
    $birth_date       = $_POST['birth_date']; 
    $country_id       = intval($_POST['country']); 
    $email            = $_POST['email'];
    $username         = $_POST['username'];
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $activationCode  = hash("sha256", random_bytes(32));
        $active          = 0;

        $sql = "INSERT INTO users (first_name, last_name, birth_date, country_id, email, username, password, active, activationCode)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $error_message = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("sssssssis", $first_name, $last_name, $birth_date, $country_id, $email, $username, $hashed_password, $active, $activationCode);

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
                    $success_message = "Registered successfully! Please check your email.";
                } catch (Exception $e) {
                    $error_message = "Mail error: " . $mail->ErrorInfo;
                }
            } else {
                $error_message = "Registration failed. Try again.";
            }

            $stmt->close();
        }
        $conn->close();
    }
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

            <?php if (!empty($error_message)): ?>
                <div class="error-box"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="success-box"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="register-form">
                <div class="input-group">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" name="first_name" placeholder="Enter your First Name" required>
                    <div id="first-name-error" class="error-message"></div>
                </div>
                <div class="input-group">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" name="last_name" placeholder="Enter your Last Name" required>
                    <div id="last-name-error" class="error-message"></div>
                </div>
                <div class="input-group">
                    <label for="birth-date">Date of Birth</label>
                    <input type="date" id="birth-date" name="birth_date" required>
                    <div id="birth-date-error" class="error-message"></div>
                </div>
                <div class="input-group">
                    <label for="country">Country</label>
                    <select id="country" name="country" required>
                        <option value="">Select your country</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?php echo $country['id']; ?>"><?php echo htmlspecialchars($country['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your Email" required>
                    <div id="email-error" class="error-message"></div>
                </div>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a Username" required>
                    <div id="username-error" class="error-message"></div>
                </div>
                <div class="input-group password-container">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter a Password" required>
                    <span id="toggle-password" class="eye-button">👁️</span>
                    <div id="password-error" class="error-message"></div>   
                </div>
                <div class="input-group password-container">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Re-enter your Password" required>
                    <span id="toggle-confirm-password" class="eye-button">👁️</span>
                    <div id="confirm-password-error" class="error-message"></div>
                </div>
                <button type="submit">Register</button>
            </form>
            <p class="back-login">
                <a href="./login.php">Back to Login</a>
            </p>
        </section>
    </main>

    <script src="./../js/register.js"></script>
    <script src="./../js/reset_form.js"></script>
    <!-- <script>
        document.getElementById("country-search").addEventListener("input", function() {
            let search = this.value.toLowerCase();
            let options = document.querySelectorAll("#country option");
            options.forEach(option => {
                option.style.display = option.text.toLowerCase().includes(search) ? "block" : "none";
            });
        });
    </script> -->
</body>
</html>
