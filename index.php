<?php
session_start();
require './php/config.php';

$isLoggedIn = isset($_SESSION["user_id"]);
$username = $isLoggedIn ? htmlspecialchars($_SESSION["username"]) : null;
$profileImage = "./uploads/default.png"; // Imagen por defecto

if ($isLoggedIn) {
    $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    
    if (!empty($userData["profile_image"]) && file_exists(__DIR__ . "/uploads/" . basename($userData["profile_image"]))) {
        $profileImage = "./uploads/" . basename($userData["profile_image"]); // Ruta corregida
    }
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}

if (!$isLoggedIn) {
    header("Location: ./web/login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gameverse</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="icon" type="image/x-icon" href="./img/GV.ico">
</head>
<body>  
    <header>
        <nav>
            <div class="logo">
                <img src="./img/logo.png" alt="logo">
            </div>
            <form class="search-bar" action="#" method="GET">
                <input type="text" name="query" placeholder="Search..." aria-label="Search">
                <button type="submit">🔍</button>
            </form>
            <div class="auth-links">
                <?php if ($isLoggedIn): ?>
                    <div class="welcome-message">Welcome, <?php echo $username; ?>!</div>
                    <img src="<?php echo htmlspecialchars($profileImage); ?>" width="35" style="border-radius: 50%;" alt="Perfil">
                    <a href="./php/profile.php">Perfil</a>
                    <a href="?logout=true">Logout</a>
                <?php else: ?>
                    <a href="./php/register.php">Register</a>
                    <a href="./php/login.php">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
</body>
</html>
