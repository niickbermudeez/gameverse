<?php
    session_start();
    require './config.php';

    $isLoggedIn   = isset($_SESSION["user_id"]);
    $username     = $isLoggedIn ? htmlspecialchars($_SESSION["username"]) : null;
    $profileImage = "./../uploads/default.png";

    $ruta = getcwd();

    if ($isLoggedIn) {
        $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();
        $result   = $stmt->get_result();
        $userData = $result->fetch_assoc();

        if (!empty($userData["profile_image"]) && file_exists(dirname(__DIR__) . "/uploads/" . basename($userData["profile_image"]))) {
            $profileImage = "./../uploads/" . basename($userData["profile_image"]);
        }
    }

    if (isset($_GET['logout'])) {
        session_unset();
        session_destroy();
        header('Location: ./../index.php');
        exit();
    }

    if (! $isLoggedIn) {
        header("Location: ./login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About us - Gameverse</title>
    <link rel="stylesheet" href="./../css/about_us.css">
    <link rel="shortcut icon" href="./../img/GV.png" type="image/x-icon">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <img src="../img/logo.png" alt="logo" class="logo-img">
            </div>
            <div class="nav-spacer"></div>
            <div class="auth-links">
                <?php if ($isLoggedIn): ?>
                    <div class="nav-right">
                        <a href="./profile.php">Profile</a>
                        <a href="./community.php">Community</a>
                        <a href="./about_us.php">About Us</a>
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
        <section class="about-us">
            <h1>About Us</h1>
            <p>Welcome to Gameverse, your ultimate destination for all things gaming! We are a passionate team of gamers and developers dedicated to bringing you the best gaming experience possible.</p>
            <p>At Gameverse, we believe in the power of community. Our platform is designed to connect gamers from around the world, allowing you to share your experiences, tips, and tricks with fellow enthusiasts.</p>
            <p>Join us on this exciting journey as we explore the latest games, trends, and innovations in the gaming industry. Whether you're a casual gamer or a hardcore enthusiast, there's something for everyone at Gameverse!</p>
        </section>
        <section class="our-team">
            <h2>Meet the Team</h2>
            <div class="team-photo">
                <img src="./../img/nick_miguel.jpg" alt="Our Team">
                <p class="team-caption">The creators behind Gameverse</p>
            </div>
            <div class="team-members">
                <div class="member">
                    <img src="./../img/Travis-Scott-3.jpg" alt="Member 1">
                    <h3>Nick Bermúdez</h3>
                    <p>Backend Developer - Encargado de la arquitectura del servidor, base de datos y seguridad.</p>
                </div>
                <div class="member">
                    <img src="./../img/Travis-Scott-3.jpg" alt="Member 2">
                    <h3>Miguel Euceda</h3>
                    <p>Frontend Developer - Diseño e implementación de la interfaz de usuario y experiencia UX/UI.</p>
                </div>
            </div>
        </section>

        <section class="technologies-used">
            <h2>Technologies We Use</h2>
            <div class="tech-gallery">
                <img src="./../img/php_logo.png" alt="PHP">
                <img src="./../img/html_logo.png" alt="HTML">
                <img src="./../img/css_logo.png" alt="CSS3">
                <img src="./../img/js_logo.png" alt="JavaScript">
                <img src="./../img/mysql_logo.png" alt="MySQL">
                <img src="./../img/github_logo.png" alt="GitHub">
                <img src="./../img/godot_logo.png" alt="Godot">
                <!-- Agrega más si lo necesitas -->
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <p>&copy;<?php echo date("Y"); ?> Gameverse. All rights reserved</p>
            <p>Developed by your trusted team.</p>
        </div>
    </footer>

    <script>
    const header = document.querySelector("header");
    let lastScroll = 0;
    const scrollThreshold = 10; // sensibilidad

    window.addEventListener("scroll", () => {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

        // No hacer nada si el scroll es muy pequeño
        if (Math.abs(currentScroll - lastScroll) <= scrollThreshold) return;

        if (currentScroll > lastScroll && currentScroll > 100) {
            // Scroll hacia abajo y pasamos cierto umbral
            header.classList.remove("show-header");
            header.classList.add("hide-header");
        } else if (currentScroll < lastScroll) {
            // Scroll hacia arriba
            header.classList.remove("hide-header");
            header.classList.add("show-header");
        }

        lastScroll = currentScroll;
    });

    // Mostrar el header al cargar la página
    window.addEventListener("DOMContentLoaded", () => {
        header.classList.add("show-header");
    });
</script>
</body>
</html>