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
    
    <style>
        /* Import Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap');

        @font-face {
            font-family: 'GoodDog';
            src: url(./fonts/good_dog/GOODDP__.TTF) format('truetype');
        }

        @font-face {
            font-family: 'HelveticaNow';
            src: url(./fonts/helvetica-now-font-family-1727953346-0/helveticanowtext-black-demo.ttf) format('truetype');
        }

        /* Reset + General layout */
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Quicksand", sans-serif;
        }

        body {
            width: 100vw;
            min-height: 100vh;
            background: radial-gradient(circle, #1B1E56, #0D0D2B);
            color: #E5E5E5;
            overflow-x: hidden;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #0f1123;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #ff0080, #7928ca);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #ff66a5, #9b4dff);
        }

        /* Header & Navigation */
        .navbar {
            background-color: rgba(0, 0, 0, 0.8) !important;
            color: #fff;
            height: 4.375rem;
            box-shadow: 0 0.25rem 0.625rem rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
            transition: transform 0.4s ease-in-out;
            border: none;
        }

        .navbar.hide-header {
            transform: translateY(-100%);
        }

        .navbar.show-header {
            transform: translateY(0);
        }

        .navbar-brand img {
            height: 1.5rem;
            transition: transform 0.3s ease;
        }

        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 500;
            padding: 0.375rem 0;
            border-bottom: 0.125rem solid transparent;
            transition: border-color 0.3s ease;
            font-size: 1rem;
            margin: 0 0.5rem;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            border-color: #F72585;
            color: #fff !important;
        }

        .welcome-message {
            font-size: 0.95rem;
            color: #ccc;
            margin-right: 10px;
        }

        .profile-pic {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 50%;
            object-fit: cover;
            border: 0.125rem solid #F72585;
        }

        .navbar-toggler {
            border: none;
            padding: 0.25rem 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('./../img/gaming-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            color: #F72585;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            color: #E5E5E5;
        }

        .section-padding {
            padding: 80px 0;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #F72585;
            margin-bottom: 3rem;
            text-align: center;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #ff0080, #7928ca);
            border-radius: 2px;
        }

        /* About Section */
        .about-card {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin-bottom: 30px;
            border: 1px solid rgba(247, 37, 133, 0.2);
        }

        .about-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #E5E5E5;
            margin-bottom: 20px;
        }

        .about-text i {
            color: #F72585;
        }

        /* Team Section */
        .team-section {
            background: rgba(13, 13, 43, 0.3);
        }

        .team-photo-container {
            text-align: center;
            margin-bottom: 50px;
        }

        .team-photo {
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            max-width: 100%;
            height: auto;
        }

        .team-caption {
            font-style: italic;
            color: #ccc;
            margin-top: 15px;
            font-size: 1.1rem;
        }

        .member-card {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(247, 37, 133, 0.2);
            height: 100%;
        }

        .member-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(247, 37, 133, 0.3);
            border-color: #F72585;
        }

        .member-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 4px solid #F72585;
        }

        .member-name {
            font-size: 1.5rem;
            font-weight: bold;
            color: #F72585;
            margin-bottom: 10px;
        }

        .member-role {
            color: #E5E5E5;
            font-size: 1rem;
            line-height: 1.6;
        }

        .member-role i {
            color: #F72585;
        }

        /* Technologies Section */
        .tech-section {
            background: rgba(27, 30, 86, 0.3);
        }

        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .tech-item {
            text-align: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(247, 37, 133, 0.1);
        }

        .tech-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(247, 37, 133, 0.3);
            border-color: #F72585;
        }

        .tech-img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 15px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .tech-name {
            font-size: 0.9rem;
            font-weight: 500;
            color: #E5E5E5;
        }

        /* Footer */
        .footer {
            background: #0D0D2B;
            color: #E5E5E5;
            padding: 40px 0;
            text-align: center;
        }

        .footer p {
            margin-bottom: 10px;
            opacity: 0.8;
            font-size: 0.9rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .about-card {
                padding: 25px;
            }
            
            .tech-grid {
                grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
                gap: 20px;
            }

            .hero-section {
                padding: 80px 0 60px 0;
            }

            .section-padding {
                padding: 60px 0;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .tech-grid {
                grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
                gap: 15px;
            }
        }
    </style>
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