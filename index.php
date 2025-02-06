<?php
session_start();
$isLoggedIn = isset($_SESSION["user_id"]);
$username = $isLoggedIn ? htmlspecialchars($_SESSION["username"]) : null;

if (!$isLoggedIn) {
    header("Location: ./web/login.html");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">    
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
                <input type="text" name="query" placeholder="Buscar..." aria-label="Search">
                <button type="submit">🔍</button>
            </form>
            <div class="auth-links">
                <?php if ($isLoggedIn): ?>
                    <div class="welcome-message">Welcome, <?php echo $username; ?>!</div>
                    <a href="?logout=true">Logout</a>
                <?php else: ?>
                    <a href="./web/register.html">Register</a>
                    <a href="./web/login.html">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <?php
    if (isset($_GET['logout'])) {
        session_unset(); 
        session_destroy(); 
        header('Location: index.php'); 
        exit();
    }
    ?>
</body>
</html>
