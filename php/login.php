<?php
session_start();
include 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        header("Location: ./../web/login.html?error=Database error");
        exit();
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            header("Location: ./../index.php?success=Login successful! Welcome, $username");
            $ruta = getcwd();
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
