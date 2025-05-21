<?php
session_start();
include 'config.php';

$isLoggedIn = isset($_SESSION["user_id"]);
$username = $isLoggedIn ? htmlspecialchars($_SESSION["username"]) : null;
$profileImage = "./uploads/default.png";

if ($isLoggedIn) {
    $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();

    if ($userData != null) {
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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community - Gameverse</title>
    <link rel="stylesheet" href="./../css/community.css">
    <link rel="icon" type="image/x-icon" href="./img/GV.ico">
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
            <img src="../img/menu.png" class="mobile-menu-icon js-mobileMenu" alt="Menu">
        </nav>
    </header>

    <main>
        <h2>COMMUNITY</h2>

        <div class="posts-container">
            <?php if ($publications->num_rows > 0): ?>
                <?php while ($post = $publications->fetch_assoc()):
                    $postImage = !empty($post["image"]) ? htmlspecialchars($post["image"]) : null;
                    $userImage = !empty($post["profile_image"]) ? htmlspecialchars($post["profile_image"]) : "./uploads/default.png";

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
                            <img src="<?php echo $userImage; ?>" class="profile-pic" alt="Perfil">
                            <span class="username"><?php echo htmlspecialchars($post["username"]); ?></span>
                            <span class="post-date"><?php echo date("d/m/Y H:i", strtotime($post["publication_date"])); ?></span>
                            <?php if ($isLoggedIn && $post['user_id'] == $_SESSION['user_id']): ?>
                                <form method="POST" action="" class="delete-form">
                                    <input type="hidden" name="delete_post_id" value="<?php echo $post["id"]; ?>">
                                    <button type="submit" class="delete-btn">❌</button>
                                </form>
                            <?php endif; ?> 
                        </div>
                        <div class="post-content">
                            <?php if ($postImage): ?>
                                <img src="<?php echo $postImage; ?>" class="post-image" alt="Publicación">
                            <?php endif; ?>
                            <p><?php echo nl2br(htmlspecialchars($post["text_description"])); ?></p>
                            <div class="reactions-container">
                                <form method="POST" action="">
                                    <input type="hidden" name="like_post_id" value="<?php echo $post["id"]; ?>">
                                    <button type="submit" class="like-btn <?php echo $userLiked ? 'liked' : ''; ?>">
                                        ❤️ <?php echo $like_count; ?>
                                    </button>
                                </form>
                                <button onclick="toggleCommentInput(<?php echo $post['id']; ?>)">🗯 <?php echo $comment_count; ?></button>
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
        <img src="<?php echo !empty($comment['profile_image']) ? htmlspecialchars($comment['profile_image']) : './uploads/default.png'; ?>" class="profile-pic" alt="Perfil">
        
        <div class="comment-details">
            <span class="comment-username"><?php echo htmlspecialchars($comment["username"]); ?></span>
            <span class="comment-date"><?php echo date("d/m/Y H:i", strtotime($comment["comment_date"])); ?></span>
        </div>
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
                                    <textarea class="comment-input" name="comment_text" placeholder="Write a comment..." required></textarea>
                                    <button type="submit" name="submit_comment" class="comment-btn">Post</button>
                                </form>
                            </div>   
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>There are no posts yet.</p>
            <?php endif; ?>
        </div>

        <a href="./create-publication.php" class="create-post-btn">+</a>
    </main>

    <script src="./../js/header.js"></script>
    <script>
    function toggleCommentInput(postId) {
        var commentBox = document.getElementById("comment-input-" + postId);
        if (commentBox.style.display === "none" || commentBox.style.display === "") {
            commentBox.style.display = "block";
        } else {
            commentBox.style.display = "none";
        }
    }

    
</script>
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
