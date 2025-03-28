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
    
    if (!empty($userData["profile_image"]) && file_exists(__DIR__ . basename($userData["profile_image"]))) {
        $profileImage = "./uploads/" . basename($userData["profile_image"]); 
    }
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}

if (!$isLoggedIn) {
    header("Location: ./php/login.php");
    exit();
}

$stmt = $conn->prepare("
    SELECT publications.*, users.username, users.profile_image 
    FROM publications 
    JOIN users ON publications.user_id = users.id 
    ORDER BY publications.publication_date DESC 
    LIMIT 15
");
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
                <img src="./../img/logo.png" alt="logo">
            </div>
            <form class="search-bar" action="#" method="GET">
                <input type="text" name="query" placeholder="Search..." aria-label="Search">
                <button type="submit">🔍</button>
            </form>
            <div class="auth-links">
                <?php if ($isLoggedIn): ?>
                    <div class="welcome-message">Welcome, <?php echo $username; ?>!</div>
                    <img src="<?php echo htmlspecialchars($profileImage); ?>" width="35" style="border-radius: 50%;" alt="Perfil">
                    <a href="./profile.php">Perfil</a>
                    <a href="./../index.php">Home</a>
                    <a href="?logout=true">Logout</a>
                <?php else: ?>
                    <a href="./php/register.php">Register</a>
                    <a href="./php/login.php">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <h2>COMMUNITY</h2>

        <div class="posts-container">
            <?php if ($publications->num_rows > 0): ?>
                <?php while ($post = $publications->fetch_assoc()): 
                    $postImage = !empty($post["image"]) ? htmlspecialchars($post["image"]) : null;
                    $userImage = !empty($post["profile_image"]) ? "./uploads/" . htmlspecialchars($post["profile_image"]) : "./uploads/default.png";
                ?>
                    <div class="post">
                        <div class="post-header">
                            <img src="<?php echo $userImage; ?>" class="profile-pic" alt="Perfil">
                            <span class="username"><?php echo htmlspecialchars($post["username"]); ?></span>
                            <span class="post-date"><?php echo date("d/m/Y H:i", strtotime($post["publication_date"])); ?></span>
                        </div>
                        <div class="post-content">
                            <?php if ($postImage): ?>
                                <img src="<?php echo $postImage; ?>" class="post-image" alt="Publicación">
                            <?php endif; ?>
                            <p><?php echo nl2br(htmlspecialchars($post["text_description"])); ?></p>
                            </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay publicaciones aún.</p>
            <?php endif; ?>
        </div>

        <a href="./create-publication.php" class="create-post-btn">+</a>
    </main>
</body>
</html>
