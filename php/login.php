<?php
session_start();
include 'config.php'; 

require './../vendor/autoload.php';
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_input = $_POST['username']; 
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, active FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $_SESSION["error"] = "⚠️ Database error: " . $conn->error;
        header("Location: ./../php/login.php");
        exit();
    }

    $stmt->bind_param("ss", $login_input, $login_input);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password, $active);
        $stmt->fetch();

        if ($active == 0) {
            $_SESSION["error"] = "⚠️ Account not activated. Please check your email.";
            header("Location: ./../php/login.php");
            exit();
        }

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;

            $update_sql = "UPDATE users SET lastSignIn = NOW() WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            if ($update_stmt) {
                $update_stmt->bind_param("i", $id);
                $update_stmt->execute();
                $update_stmt->close();
            }

            $_SESSION["success"] = "Login successful! Welcome, $username";
            header("Location: ./../index.php");
            exit();
        } else {
            $_SESSION["error"] = "⚠️ Incorrect password";
        }
    } else {
        $_SESSION["error"] = "⚠️ User not found";
    }

    $stmt->close();
    $conn->close();
    header("Location: ./../php/login.php");
    exit();
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gameverse</title>
    <link rel="stylesheet" href="./../css/login.css">
    <link rel="shortcut icon" href="./../img/GV.png" type="image/x-icon">
</head>
<body>
    <main>
        <div class="logo">
            <img src="./../img/logo.png" alt="logo" class="logo-img">
        </div>
        <section class="login-form-container">
            <h1>Welcome Back!</h1>

            <?php if (isset($_SESSION["error"])): ?>
                <div class="error-message"><p><?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?></p></div>
            <?php endif; ?>

            <form action="./login.php" method="POST" class="login-form">
                <div class="input-group">
                    <label for="username">Email or Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your Email or Username" required>
                </div>
                
                <div class="input-group password-container">
                    <label for="password">Password</label>
                    <input type="password" id="password-input" name="password" placeholder="Enter a Password" required>
                    <span id="toggle-password-button" class="eye-button">👁️</span>
                </div>
                
                <button type="submit">Login</button>
            </form>
            
            <p class="redirect">
                Don't have an account? <a href="./../php/register.php">Register here</a>
            </p>
            <p class="forgot-password">
                <a href="./reset-password.php">Forgot your password?</a>
            </p>
        </section>  
    </main>
    <script src="./../js/login.js"></script>
</body>
</html>
