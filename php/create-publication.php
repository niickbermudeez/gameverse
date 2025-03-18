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

    if (!empty($userData["profile_image"]) && file_exists(__DIR__ . "/uploads/" . basename($userData["profile_image"]))) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $image = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image']['name'];
        $uploadDir = './uploads/';
        $uploadFile = $uploadDir . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);
    }

    $stmt = $conn->prepare("INSERT INTO publications (user_id, text_description, image) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_SESSION["user_id"], $description, $image);
    if ($stmt->execute()) {
        echo "<script>alert('Publicación creada exitosamente'); window.location.href='community.php';</script>";
    } else {
        echo "<script>alert('Error al crear la publicación.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./../css/create-publication.css">
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
        <form action="create_publication.php" method="POST" enctype="multipart/form-data" class="create-publication-form">
            <textarea name="description" placeholder="Describe your publication..." required></textarea>

            <label for="image">Upload an image (optional):</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit">Post</button>
        </form>
    </main>
</body>
</html>
