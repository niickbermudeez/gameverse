<?php
session_start();
require 'config.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Obtener los datos del usuario desde la base de datos
$sql = "SELECT username, bio, country_id, birth_date, profile_image FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Obtener lista de países desde la base de datos
$sql_countries = "SELECT id, name FROM countries";  // Asumiendo que tienes una tabla 'countries'
$stmt_countries = $conn->prepare($sql_countries);
$stmt_countries->execute();
$countries_result = $stmt_countries->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST["username"]);
    $bio = isset($_POST["bio"]) ? htmlspecialchars($_POST["bio"]) : '';  // Permitir bio vacía
    $country_id = (int)$_POST["country"];  // Asegurarse de capturar el id del país seleccionado
    $birthdate = htmlspecialchars($_POST["birth_date"]);
    $profile_image = $user["profile_image"]; // Mantener la imagen existente si no se sube una nueva

    // Verificar si el nombre de usuario ya está en uso
    $sql_check = "SELECT id FROM users WHERE username = ? AND id != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("si", $username, $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
        die("El nombre de usuario ya está en uso.");
    }

    // Subir nueva imagen de perfil si se selecciona
    if (!empty($_FILES["profile_image"]["name"])) {
        $target_dir = "./../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Crear directorio si no existe
        }

        $file_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verificar tipos de archivo permitidos
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            die("Formato de imagen no válido. Usa JPG, PNG o GIF.");
        }

        // Mover archivo a la carpeta de destino
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = $target_file; // Actualizar imagen de perfil
        } else {
            die("Error al subir la imagen. Verifica los permisos de la carpeta.");
        }
    }

    // Actualizar datos del usuario en la base de datos
    $sql = "UPDATE users SET username=?, bio=?, country_id=?, birth_date=?, profile_image=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $username, $bio, $country_id, $birthdate, $profile_image, $user_id);

    if ($stmt->execute()) {
        header("Location: profile.php?success=1"); // Redirigir a la misma página con mensaje de éxito
        exit();
    } else {
        die("Error al actualizar el perfil.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

            <!-- Previsualización de la imagen de perfil -->
            <?php if (!empty($user["profile_image"])): ?>
                <div class="profile-preview-container">
                    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" class="profile-preview" alt="Vista previa del perfil">
                </div>
            <?php endif; ?>

            <!-- Mensaje de éxito -->
            <?php if (isset($_GET['success'])): ?>
                <p class="success-message">Perfil actualizado correctamente.</p>
            <?php endif; ?>

            <!-- Formulario para editar el perfil -->
            <form action="profile.php" method="POST" enctype="multipart/form-data" class="profile-form">
                <div class="input-group">
                    <label>Nombre de usuario</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    <div id="username-error" class="error-message"></div>
                </div>
                <div class="input-group">
                    <label>Biografía</label>
                    <textarea name="bio"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                    <div id="bio-error" class="error-message"></div>
                </div>
                <div class="input-group">
                    <label>País</label>
                    <select name="country" required>
                        <?php while ($country = $countries_result->fetch_assoc()): ?>
                            <option value="<?php echo $country['id']; ?>" <?php echo ($user['country_id'] == $country['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($country['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <div id="country-error" class="error-message"></div>
                </div>
                <div class="input-group">
                    <label>Fecha de nacimiento</label>
                    <input type="date" name="birthdate" value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($user['birth_date']))); ?>" required>
                    <div id="birthdate-error" class="error-message"></div>
                </div>
                <div class="input-group">
                    <label>Imagen de perfil</label>
                    <input type="file" name="profile_image">
                </div>
                <button type="submit">Guardar cambios</button>
                <p class="back-home">
                    <a href="./../index.php">Volver a inicio</a>
                </p>
            </form>
        </section>
    </main>
    <script src="./../js/profile.js"></script>
</body>
</html>
