<?php
    session_start();
    require '../php/config.php';

    $isLoggedIn   = isset($_SESSION["user_id"]);
    $username     = $isLoggedIn ? htmlspecialchars($_SESSION["username"]) : null;
    $profileImage = ".././uploads/default.png";

    if ($isLoggedIn) {
        $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();
        $result   = $stmt->get_result();
        $userData = $result->fetch_assoc();

        if ($userData != null) {
        $profileImage = "./../uploads/" . basename($userData["profile_image"]);
    }
    }

    if (isset($_GET['logout'])) {
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit();
    }

    if (! $isLoggedIn) {
        header("Location: ./php/login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="game-title">Juego</title>
    <link rel="stylesheet" href="../css/GameContainer.css">
</head>
<body>
<header>
        <nav>
            <div class="logo">
                <img src=".././img/logo.png" alt="logo" class="logo-img">
            </div>
            <div class="nav-spacer"></div>
            <div class="auth-links">
                <?php if ($isLoggedIn): ?>
                    <div class="nav-right">
                        <a href=".././php/profile.php">Profile</a>
                        <a href=".././php/community.php">Community</a>
                        <a href=".././php/about_us.php">About Us</a>
                        <a href="?logout=true">Logout</a>
                        <div class="welcome-message">Welcome,<?php echo $username; ?>!</div>
                        <img src="<?php echo htmlspecialchars($profileImage); ?>" class="profile-pic" alt="Perfil">
                    </div>
                <?php else: ?>
                    <a href="./php/register.php">Register</a>
                    <a href="./php/login.php">Login</a>
                <?php endif; ?>
            </div>
            <img src="./img/menu.png" class="mobile-menu-icon js-mobileMenu" alt="Menu">
        </nav>
    </header>
    <main>


    </main>

<script>
    // Recuperar los datos de localStorage
    const gameUrl = localStorage.getItem('gameUrl');
    const gameName = localStorage.getItem('gameName');
    
    if (gameUrl && gameName) {
        document.querySelector("main").innerHTML = `
    <h1>${gameName}</h1>
    <iframe src="${gameUrl}" width="800" height="600" style="border:none;"></iframe>
`;

    } else {
        document.body.innerHTML = "<p>No hay juego seleccionado.</p>";
    }
</script>

</body>
</html>