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
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Font Awesome para íconos adicionales -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="shortcut icon" href="./../img/GV.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/about_us.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top show-header">
        <div class="container-fluid px-3">
            <a class="navbar-brand" href="#">
                <img src="../img/logo.png" alt="Gameverse Logo">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto d-flex align-items-center">
                    <?php if ($isLoggedIn): ?>
                        <a class="nav-link" href="./../index.php">
                            Home
                        </a>
                        <a class="nav-link" href="./profile.php">
                            Profile
                        </a>
                        <a class="nav-link" href="./community.php">
                            Community
                        </a>
                        <a class="nav-link active" href="./about_us.php">
                            About Us
                        </a>
                        <a class="nav-link" href="?logout=true">
                            Logout
                        </a>
                        <span class="welcome-message ms-3">Welcome, <?php echo $username; ?>!</span>
                        <img src="<?php echo htmlspecialchars($profileImage); ?>" class="profile-pic ms-2" alt="Profile">
                    <?php else: ?>
                        <a class="nav-link" href="./register.php">Register</a>
                        <a class="nav-link" href="./login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="hero-title">About Gameverse</h1>
                        <p class="hero-subtitle">Your Ultimate Gaming Community</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Us Section -->
        <section class="section-padding">
            <div class="container">
                <h2 class="section-title">Who We Are</h2>
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="about-card">
                            <p class="about-text">
                                <i class="fas fa-gamepad me-2"></i>
                                Welcome to Gameverse, your ultimate destination for all things gaming! We are a passionate team of gamers and developers dedicated to bringing you the best gaming experience possible.
                            </p>
                            <p class="about-text">
                                <i class="fas fa-users me-2"></i>
                                At Gameverse, we believe in the power of community. Our platform is designed to connect gamers from around the world, allowing you to share your experiences, tips, and tricks with fellow enthusiasts.
                            </p>
                            <p class="about-text">
                                <i class="fas fa-rocket me-2"></i>
                                Join us on this exciting journey as we explore the latest games, trends, and innovations in the gaming industry. Whether you're a casual gamer or a hardcore enthusiast, there's something for everyone at Gameverse!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section class="section-padding team-section">
            <div class="container">
                <h2 class="section-title">Meet the Team</h2>
                
                <div class="team-photo-container">
                    <img src="./../img/nick_miguel.jpg" alt="Our Team" class="team-photo img-fluid">
                    <p class="team-caption">The creators behind Gameverse</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card member-card">
                            <img src="./../img/Travis-Scott-3.jpg" alt="Nick Bermúdez" class="member-img mx-auto">
                            <h3 class="member-name">Nick Bermúdez</h3>
                            <p class="member-role">
                                <i class="fas fa-server me-2"></i>
                                <strong>Backend Developer</strong><br>
                                Encargado de la arquitectura del servidor, base de datos y seguridad.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card member-card">
                            <img src="./../img/Travis-Scott-3.jpg" alt="Miguel Euceda" class="member-img mx-auto">
                            <h3 class="member-name">Miguel Euceda</h3>
                            <p class="member-role">
                                <i class="fas fa-paint-brush me-2"></i>
                                <strong>Frontend Developer</strong><br>
                                Diseño e implementación de la interfaz de usuario y experiencia UX/UI.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Technologies Section -->
        <section class="section-padding tech-section">
            <div class="container">
                <h2 class="section-title">Technologies We Use</h2>
                <div class="tech-grid">
                    <div class="tech-item">
                        <img src="./../img/php_logo.png" alt="PHP" class="tech-img">
                        <div class="tech-name">PHP</div>
                    </div>
                    <div class="tech-item">
                        <img src="./../img/html_logo.png" alt="HTML" class="tech-img">
                        <div class="tech-name">HTML5</div>
                    </div>
                    <div class="tech-item">
                        <img src="./../img/css_logo.png" alt="CSS" class="tech-img">
                        <div class="tech-name">CSS3</div>
                    </div>
                    <div class="tech-item">
                        <img src="./../img/js_logo.png" alt="JavaScript" class="tech-img">
                        <div class="tech-name">JavaScript</div>
                    </div>
                    <div class="tech-item">
                        <img src="./../img/mysql_logo.png" alt="MySQL" class="tech-img">
                        <div class="tech-name">MySQL</div>
                    </div>
                    <div class="tech-item">
                        <img src="./../img/github_logo.png" alt="GitHub" class="tech-img">
                        <div class="tech-name">GitHub</div>
                    </div>
                    <div class="tech-item">
                        <img src="./../img/godot_logo.png" alt="Godot" class="tech-img">
                        <div class="tech-name">Godot</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p>&copy; <?php echo date("Y"); ?> Gameverse. All rights reserved</p>
                    <p>Developed by your trusted team.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Header scroll behavior
        const navbar = document.querySelector(".navbar");
        let lastScroll = 0;
        const scrollThreshold = 10;

        window.addEventListener("scroll", () => {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            if (Math.abs(currentScroll - lastScroll) <= scrollThreshold) return;

            if (currentScroll > lastScroll && currentScroll > 100) {
                navbar.classList.remove("show-header");
                navbar.classList.add("hide-header");
            } else if (currentScroll < lastScroll) {
                navbar.classList.remove("hide-header");
                navbar.classList.add("show-header");
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