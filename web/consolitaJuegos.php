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
        header("Location: ./php/login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="game-title">Game - Gameverse</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Font Awesome para íconos adicionales -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="shortcut icon" href="./../img/GV.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/GameContainer.css">
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
                        <a class="nav-link" href="./../php/profile.php">
                            Profile
                        </a>
                        <a class="nav-link" href="./../php/community.php">
                            Community
                        </a>
                        <a class="nav-link" href="./../php/about_us.php">
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

    <main>
        <div class="game-container">
            <div class="game-header">
                <button onclick="history.back()" class="back-btn">
                        Back to Games
                </button>
                <h1 id="game-title-display" class="game-title">Loading Game...</h1>
            </div>
            
            <div class="game-wrapper">
                <div id="game-content" class="game-content">
                    <div class="loading-spinner">
                        <i class="bi bi-controller"></i>
                        <p>Loading your game...</p>
                    </div>
                </div>
            </div>
            
            <div class="game-controls">
                <button onclick="toggleFullscreen()" class="control-btn">
                    <i class="bi bi-arrows-angle-expand"></i> Fullscreen
                </button>
                <button onclick="refreshGame()" class="control-btn">
                    <i class="bi bi-arrow-clockwise"></i> Restart
                </button>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p>&copy; <?php echo date("Y"); ?> Gameverse. All rights reserved</p>
                    <p>Enjoy your gaming experience!</p>
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
            
            // Scroll to game area after a short delay
            setTimeout(() => {
                const gameContainer = document.querySelector('.game-container');
                if (gameContainer) {
                    gameContainer.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }
            }, 500);
        });

        // Game loading logic
        const gameUrl = localStorage.getItem('gameUrl');
        const gameName = localStorage.getItem('gameName');
        
        if (gameUrl && gameName) {
            document.getElementById('game-title').textContent = `${gameName} - Gameverse`;
            document.getElementById('game-title-display').textContent = gameName;
            
            setTimeout(() => {
                document.getElementById('game-content').innerHTML = `
                    <iframe id="game-iframe" src="${gameUrl}" allowfullscreen></iframe>
                `;
            }, 1000);
        } else {
            document.getElementById('game-content').innerHTML = `
                <div class="error-message">
                    <i class="bi bi-exclamation-triangle"></i>
                    <h3>No Game Selected</h3>
                    <p>Please return to the games page and select a game to play.</p>
                    <button onclick="history.back()" class="back-btn">
                            Go Back
                    </button>
                </div>
            `;
        }

        // Game control functions
        function toggleFullscreen() {
            const iframe = document.getElementById('game-iframe');
            if (iframe) {
                if (iframe.requestFullscreen) {
                    iframe.requestFullscreen();
                } else if (iframe.webkitRequestFullscreen) {
                    iframe.webkitRequestFullscreen();
                } else if (iframe.msRequestFullscreen) {
                    iframe.msRequestFullscreen();
                }
            }
        }

        function refreshGame() {
            const iframe = document.getElementById('game-iframe');
            if (iframe) {
                iframe.src = iframe.src;
            }
        }
    </script>
</body>
</html>