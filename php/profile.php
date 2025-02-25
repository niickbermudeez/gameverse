<?php
session_start();
require 'config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST["username"]);
    $bio = htmlspecialchars($_POST["bio"]);
    $country = htmlspecialchars($_POST["country"]);
    $age = intval($_POST["age"]);
    $profile_image = null;

    $sql_check = "SELECT id FROM users WHERE username = ? AND id != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("si", $username, $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
        die("El nombre de usuario ya está en uso.");
    }

    if (!empty($_FILES["profile_image"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            die("Formato de imagen no permitido. Usa JPG, PNG o GIF.");
        }

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = $target_file;
        }
    }

    $sql = "UPDATE users SET username=?, bio=?, country=?, age=?, profile_image=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisi", $username, $bio, $country, $age, $profile_image, $user_id);
    $stmt->execute();

    header("Location: profile.php?success=1");
    exit();
}

$sql = "SELECT username, bio, country, age, profile_image FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil - Gameverse</title>
    <link rel="stylesheet" href="./../css/profile.css">
    <link rel="shortcut icon" href="./../img/GV.png" type="image/x-icon">
</head>
<body>
    <main>
        <div class="logo">
            <img src="./../img/logo.png" alt="logo">
        </div>
        <section class="profile-form-container">
            <h1>Editar Perfil</h1>
            <?php if (isset($_GET['success'])): ?>
                <p class="success-message">Perfil actualizado correctamente.</p>
            <?php endif; ?>
            <form action="profile.php" method="POST" enctype="multipart/form-data" class="profile-form">
                <div class="input-group">
                    <label>Nombre de usuario</label>
                    <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
                </div>
                <div class="input-group">
                    <label>Biografía</label>
                    <textarea name="bio"><?php echo $user['bio']; ?></textarea>
                </div>
                <div class="input-group">
                    <label>Ubicación</label>
                    <input type="text" name="country" value="<?php echo $user['country']; ?>">
                </div>
                <div class="input-group">
                    <label>Edad</label>
                    <input type="number" name="age" value="<?php echo $user['age']; ?>" required>
                </div>
                <div class="input-group">
                    <label>Imagen de perfil</label>
                    <input type="file" name="profile_image">
                    <?php if (!empty($user["profile_image"])): ?>
                        <img src="<?php echo $user['profile_image']; ?>" class="profile-preview">
                    <?php endif; ?>
                </div>
                <button type="submit">Guardar Cambios</button>
            </form>
        </section>
    </main>
</body>
</html>
