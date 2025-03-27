<?php
session_start();
include 'config.php';

$isLoggedIn = isset($_SESSION["user_id"]);
$profileImage = "./uploads/default.png"; 
$username = "Invitado";

if ($isLoggedIn) {
    $stmt = $conn->prepare("SELECT username, profile_image FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    
    $username = htmlspecialchars($userData["username"]);

    $imagePath = "./uploads/" . basename($userData["profile_image"]);
    if (!empty($userData["profile_image"]) && file_exists(__DIR__ . "/" . $imagePath)) {
        $profileImage = $imagePath;
    }
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}

if (!$isLoggedIn) {
    header("Location: php/login.php");
    exit();
}

// Inserir publicació a la bbdd
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION["user_id"];
    $textDescription = trim($_POST["text_description"]);
    $imagePath = null;

    if (empty($textDescription)) {
        echo "Error: La descripción no puede estar vacía.";
        exit();
    }

    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "./uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $imagePath = $targetFilePath;
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO publications (user_id, text_description, image) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $textDescription, $imagePath);
    
    if ($stmt->execute()) {
        header("Location: community.php");
        exit();
    } else {
        echo "Error en publicar.";
    }
}
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
        <a href="./create-publication.php" class="create-post-btn">+</a>
    </main>
</body>
</html>
