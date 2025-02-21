<?php
session_start();
include 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_input = $_POST['username']; 
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, active FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        header("Location: ./../web/login.html?error=Database error");
        exit();
    }

    $stmt->bind_param("ss", $login_input, $login_input);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password, $active);
        $stmt->fetch();

        if ($active == 0) {
            header("Location: ./../web/login.html?error=Account not activated. Please check your email.");
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

            header("Location: ./../index.php?success=Login successful! Welcome, $username");
            exit();
        } else {
            header("Location: ./../web/login.html?error=Incorrect password");
            exit();
        }
    } else {
        header("Location: ./../web/login.html?error=User not found");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?> 