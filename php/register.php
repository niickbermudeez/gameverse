<?php
session_start();
include 'config.php'; 

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $country = $_POST['country'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Expresión regular para validar la contraseña
    $password_regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.,@$!%*?&])[A-Za-z\d.,@$!%*?&]{8,}$/';

    // Variables para errores
    $error = '';

    if (!preg_match($password_regex, $password)) {
        $error = "Password must be at least 8 characters long, include uppercase, lowercase, numbers, and special characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    }

    if (!empty($error)) {
        header("Location: ./../web/register.php?error=" . urlencode($error));
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (first_name, last_name, age, country, email, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        header("Location: ./../web/register.php?error=Database error");
        exit();
    }

    $stmt->bind_param("ssissss", $first_name, $last_name, $age, $country, $email, $username, $hashed_password);

    if ($stmt->execute()) {
        header("Location: ./../web/login.html?success=Registered successfully! Please log in.");
        exit();
    } else {
        header("Location: ./../web/register.php?error=Registration failed. Try again.");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
