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
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <link rel="stylesheet" href="./css/index.css">
    <link rel="icon" type="image/x-icon" href="./img/GV.ico">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top show-header">
        <div class="container-fluid px-3">
            <a class="navbar-brand" href="#">
                <img src="./img/logo.png" alt="Gameverse Logo">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto d-flex align-items-center">
                    <?php if ($isLoggedIn): ?>
                        <a class="nav-link active" href="./index.php">
                            Home
                        </a>
                        <a class="nav-link" href="./php/profile.php">
                            Profile
                        </a>
                        <a class="nav-link" href="./php/community.php">
                            Community
                        </a>
                        <a class="nav-link" href="./php/about_us.php">
                            About Us
                        </a>
                        <a class="nav-link" href="?logout=true">
                            Logout
                        </a>
                        <span class="welcome-message ms-3">Welcome, <?php echo $username; ?>!</span>
                        <img src="<?php echo htmlspecialchars($profileImage); ?>" class="profile-pic ms-2" alt="Profile">
                    <?php else: ?>
                        <a class="nav-link" href="./php/register.php">Register</a>
                        <a class="nav-link" href="./php/login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Category Subnavigation -->
    <nav class="category-subnav">
        <div class="container-fluid">
            <div class="category-nav-wrapper">
                <div class="category-nav-scroll">
                    <a href="?category=" class="category-item <?php echo (!$categoryFilter) ? 'active' : ''; ?>">
                        <i class="fas fa-th-large"></i>
                        All Games
                    </a>
                    <?php foreach ($categories as $category): ?>
                        <a href="?category=<?php echo $category['id']; ?>" 
                           class="category-item <?php echo ($categoryFilter === $category['id']) ? 'active' : ''; ?>">
                            <i class="fas fa-gamepad"></i>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </nav>

    <main>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Swiper JS -->
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
        // Header scroll behavior
        const navbar = document.querySelector(".navbar");
        const categorySubnav = document.querySelector(".category-subnav");
        let lastScroll = 0;
        const scrollThreshold = 10;

        window.addEventListener("scroll", () => {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            if (Math.abs(currentScroll - lastScroll) <= scrollThreshold) return;

            if (currentScroll > lastScroll && currentScroll > 100) {
                navbar.classList.remove("show-header");
                navbar.classList.add("hide-header");
                categorySubnav.classList.add("hide-header");
            } else if (currentScroll < lastScroll) {
                navbar.classList.remove("hide-header");
                navbar.classList.add("show-header");
                categorySubnav.classList.remove("hide-header");
            }

            lastScroll = currentScroll;
        });

        // Show header on page load
        window.addEventListener("DOMContentLoaded", () => {
            navbar.classList.add("show-header");
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>