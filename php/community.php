<?php
session_start();
include 'config.php';

$isLoggedIn = isset($_SESSION["user_id"]);
$username = $isLoggedIn ? htmlspecialchars($_SESSION["username"]) : null;
$profileImage = "./../uploads/default.png";

if ($isLoggedIn) {
    $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
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

if (!$isLoggedIn) {
    header("Location: ./login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["like_post_id"])) {
    $user_id = $_SESSION["user_id"];
    $publication_id = intval($_POST["like_post_id"]);

    $stmt = $conn->prepare("SELECT type FROM reactions WHERE user_id = ? AND publication_id = ?");
    $stmt->bind_param("ii", $user_id, $publication_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_reaction = $result->fetch_assoc();

    if ($existing_reaction) {
        if ($existing_reaction["type"] === "Like") {
            $stmt = $conn->prepare("DELETE FROM reactions WHERE user_id = ? AND publication_id = ?");
            $stmt->bind_param("ii", $user_id, $publication_id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("UPDATE reactions SET type = 'Like' WHERE user_id = ? AND publication_id = ?");
            $stmt->bind_param("ii", $user_id, $publication_id);
            $stmt->execute();
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO reactions (user_id, publication_id, type) VALUES (?, ?, 'Like')");
        $stmt->bind_param("ii", $user_id, $publication_id);
        $stmt->execute();
    }

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit_comment"])) {
    $user_id = $_SESSION["user_id"];
    $publication_id = intval($_POST["comment_post_id"]);
    $comment_text = trim($_POST["comment_text"]);

    if (!empty($comment_text)) {
        $stmt = $conn->prepare("INSERT INTO comments (user_id, publication_id, comment_text, comment_date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $user_id, $publication_id, $comment_text);
        $stmt->execute();
    }

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_post_id"])) {
    $post_id = intval($_POST["delete_post_id"]);
    $user_id = $_SESSION["user_id"];
    $stmt = $conn->prepare("DELETE FROM publications WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_comment_id"])) {
    $comment_id = intval($_POST["delete_comment_id"]);
    $user_id = $_SESSION["user_id"];
    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $comment_id, $user_id);
    $stmt->execute();
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

$stmt = $conn->prepare("SELECT publications.*, users.username, users.profile_image FROM publications JOIN users ON publications.user_id = users.id ORDER BY publications.publication_date DESC LIMIT 20");
$stmt->execute();
$publications = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community - Gameverse</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Font Awesome para íconos adicionales -->
    <!-- CDN de Font Awesome (versión 6 o 5) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="shortcut icon" href="./../img/GV.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/community.css">
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
                        <a class="nav-link active" href="./community.php">
                            Community
                        </a>
                        <a class="nav-link" href="./about_us.php">
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
                        <h1 class="hero-title">Community</h1>
                        <p class="hero-subtitle">Connect, Share, and Game Together</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Posts Section -->
        <section class="posts-section section-padding">
            <div class="container">
                <div class="posts-container">
                    <?php if ($publications->num_rows > 0): ?>
                        <?php while ($post = $publications->fetch_assoc()):
                            $postImage = !empty($post["image"]) ? htmlspecialchars($post["image"]) : null;
                            $userImage = !empty($post["profile_image"]) ? htmlspecialchars($post["profile_image"]) : "./../uploads/default.png";

                            $stmt_likes = $conn->prepare("SELECT COUNT(*) as like_count FROM reactions WHERE publication_id = ? AND type = 'Like'");
                            $stmt_likes->bind_param("i", $post["id"]);
                            $stmt_likes->execute();
                            $result_likes = $stmt_likes->get_result();
                            $like_count = $result_likes->fetch_assoc()["like_count"] ?? 0;

                            $stmt_comments = $conn->prepare("SELECT COUNT(*) as comment_count FROM comments WHERE publication_id = ?");
                            $stmt_comments->bind_param("i", $post["id"]);
                            $stmt_comments->execute();
                            $result_comments = $stmt_comments->get_result();
                            $comment_count = $result_comments->fetch_assoc()["comment_count"] ?? 0; 

                            $userLiked = false;
                            if ($isLoggedIn) {
                                $stmt_user_like = $conn->prepare("SELECT 1 FROM reactions WHERE user_id = ? AND publication_id = ? AND type = 'Like'");
                                $stmt_user_like->bind_param("ii", $_SESSION["user_id"], $post["id"]);
                                $stmt_user_like->execute();
                                $result_user_like = $stmt_user_like->get_result();
                                $userLiked = $result_user_like->num_rows > 0;
                            }
                        ?>
                            <div class="post">
                                <div class="post-header">
                                    <img src="<?php echo $userImage; ?>" class="post-profile-pic" alt="Profile">
                                    <div class="post-user-info">
                                        <span class="post-username"><?php echo htmlspecialchars($post["username"]); ?></span>
                                        <span class="post-date"><?php echo date("M d, Y H:i", strtotime($post["publication_date"])); ?></span>
                                    </div>
                                    <?php if ($isLoggedIn && $post['user_id'] == $_SESSION['user_id']): ?>
                                        <form method="POST" action="" class="delete-form ms-auto">
                                            <input type="hidden" name="delete_post_id" value="<?php echo $post["id"]; ?>">
                                            <button type="submit" class="delete-btn">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?> 
                                </div>
                                <div class="post-content">
                                    <?php if ($postImage): ?>
                                        <img src="<?php echo $postImage; ?>" class="post-image" alt="Post Image">
                                    <?php endif; ?>
                                    <p><?php echo nl2br(htmlspecialchars($post["text_description"])); ?></p>
                                    
                                    <div class="reactions-container">
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="like_post_id" value="<?php echo $post["id"]; ?>">
                                            <button type="submit" class="reaction-btn like-btn <?php echo $userLiked ? 'liked' : ''; ?>">
                                                <i class="bi bi-heart"></i>
                                                <span><?php echo $like_count; ?></span>
                                            </button>
                                        </form>
                                        <button class="reaction-btn comment-btn" onclick="toggleCommentInput(<?php echo $post['id']; ?>)">
                                            <i class="bi bi-chat"></i>
                                            <span><?php echo $comment_count; ?></span>
                                        </button>
                                    </div>

                                    <div class="comments-section">
                                        <?php 
                                            $stmt_comments = $conn->prepare("
                                                SELECT comments.*, users.username, users.profile_image 
                                                FROM comments 
                                                JOIN users ON comments.user_id = users.id 
                                                WHERE comments.publication_id = ? 
                                                ORDER BY comments.comment_date ASC
                                            ");
                                            $stmt_comments->bind_param("i", $post["id"]);
                                            $stmt_comments->execute();
                                            $comments = $stmt_comments->get_result();
                                        ?>
                                        <?php while ($comment = $comments->fetch_assoc()): ?>
                                            <div class="comment">
                                                <div class="comment-header">
                                                    <img src="<?php echo !empty($comment['profile_image']) ? htmlspecialchars($comment['profile_image']) : './../uploads/default.png'; ?>" class="comment-profile-pic" alt="Profile">
                                                    <div class="comment-user-info">
                                                        <span class="comment-username"><?php echo htmlspecialchars($comment["username"]); ?></span>
                                                        <span class="comment-date"><?php echo date("M d, Y H:i", strtotime($comment["comment_date"])); ?></span>
                                                    </div>
                                                    <?php if ($isLoggedIn && $comment['user_id'] == $_SESSION['user_id']): ?>
                                                        <form method="POST" action="" class="delete-form ms-auto">
                                                            <input type="hidden" name="delete_comment_id" value="<?php echo $comment["id"]; ?>">
                                                            <button type="submit" class="delete-btn-small">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="comment-text">
                                                    <p><?php echo nl2br(htmlspecialchars($comment["comment_text"])); ?></p>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>

                                    <div id="comment-input-<?php echo $post['id']; ?>" class="comment-input-container" style="display: none;">
                                        <form method="POST" action="" class="comment-form">
                                            <input type="hidden" name="comment_post_id" value="<?php echo $post['id']; ?>">
                                            <div class="input-group">
                                                <textarea class="form-control comment-input" name="comment_text" placeholder="Write a comment..." required rows="2"></textarea>
                                                <button type="submit" name="submit_comment" class="btn comment-submit-btn">
                                                    <i class="bi bi-send"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>   
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-posts">
                            <i class="fas fa-comments fa-3x mb-3"></i>
                            <h3>No posts yet</h3>
                            <p>Be the first to share something with the community!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Create Post Button -->
        <a href="./create-publication.php" class="create-post-btn">
            <i class="fas fa-plus"></i>
        </a>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p>&copy; <?php echo date("Y"); ?> Gameverse. All rights reserved</p>
                    <p>Share your gaming moments with the world.</p>
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

        // Comment toggle function
        function toggleCommentInput(postId) {
            var commentBox = document.getElementById("comment-input-" + postId);
            if (commentBox.style.display === "none" || commentBox.style.display === "") {
                commentBox.style.display = "block";
                commentBox.querySelector('textarea').focus();
            } else {
                commentBox.style.display = "none";
            }
        }

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