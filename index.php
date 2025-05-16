<?php
    session_start();
    require './php/config.php';

    $isLoggedIn   = isset($_SESSION["user_id"]);
    $username     = $isLoggedIn ? htmlspecialchars($_SESSION["username"]) : null;
    $profileImage = "./uploads/default.png";

    if ($isLoggedIn) {
        $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();
        $result   = $stmt->get_result();
        $userData = $result->fetch_assoc();

        if (! empty($userData["profile_image"]) && file_exists(__DIR__ . "/uploads/" . basename($userData["profile_image"]))) {
            $profileImage = "./uploads/" . basename($userData["profile_image"]);
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

    // Cargar categorías
    $categories = [];
    $catStmt    = $conn->prepare("SELECT id, name FROM categories");
    $catStmt->execute();
    $catResult = $catStmt->get_result();
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }

    $categoryFilter = isset($_GET['category']) ? intval($_GET['category']) : null;

    // Cargar juegos
    $games = [];
    if ($categoryFilter) {
        $stmt = $conn->prepare("SELECT g.name, g.url, g.image_url, c.name AS category_name
                            FROM games g
                            LEFT JOIN categories c ON g.category_id = c.id
                            WHERE g.category_id = ?");
        $stmt->bind_param("i", $categoryFilter);
    } else {
        $stmt = $conn->prepare("SELECT g.name, g.url, g.image_url, c.name AS category_name
                            FROM games g
                            LEFT JOIN categories c ON g.category_id = c.id");
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $games[] = $row;
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
</head>
<body>
    <div class="page-wrapper">

    <header>
        <nav>
            <div class="logo">
                <img src="./img/logo.png" alt="logo" class="logo-img">
            </div>
            <div class="nav-spacer"></div>
            <div class="auth-links">
                <?php if ($isLoggedIn): ?>
                    <div class="nav-right">
                        <a href="./php/profile.php">Profile</a>
                        <a href="./php/community.php">Community</a>
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
<div>
    <form method="GET" class="filter-container">
        <label for="category">Filtrar por categoría:</label>
        <select name="category" id="category" onchange="this.form.submit()">
            <option value="">Todas</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>"<?php echo($categoryFilter === $category['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

    <div class="swiper-container">
        <div class="swiper-wrapper">
            <?php foreach ($games as $game): ?>
                <div class="swiper-slide">
                    <img src="<?php echo htmlspecialchars($game['image_url']); ?>" alt="<?php echo htmlspecialchars($game['name']); ?>">
                    <div class="title"><span><?php echo htmlspecialchars($game['name']); ?></span></div>
                    <button class="play-button" onclick="cargarJuego('<?php echo htmlspecialchars($game['url']); ?>', '<?php echo htmlspecialchars($game['name']); ?>')">▶ Play</button>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>


</main>

<footer>
    <div class="footer-content">
        <p>&copy;<?php echo date("Y"); ?> Gameverse. Todos los derechos reservados.</p>
        <p>Desarrollado por tu equipo de confianza.</p>
    </div>
</footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="./js/main.js" defer></script>
    <script src="./js/CardsFuncionalidad.js"></script>

    <script>
        function cargarJuego(url, nombre) {
            // Guardar en localStorage
            localStorage.setItem('gameUrl', url);
            localStorage.setItem('gameName', nombre);

            // Redirigir a la otra página
            window.location.href = 'prueba.html';
        }
    </script>

    <script>
    let lastScrollTop = 0;
    const header = document.querySelector("header");

    window.addEventListener("scroll", () => {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop) {
            // Scrolling down
            header.style.top = "-80px"; // Ajusta si tu header es más alto
        } else {
            // Scrolling up
            header.style.top = "0";
        }

        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // Evita valores negativos
    });
</script>

</div>
</body>
<script src="./js/header.js"></script>
</html>
