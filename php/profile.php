<?php
session_start();
require 'config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT username, bio, country, birth_date, profile_image FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST["username"]);
    $bio = htmlspecialchars($_POST["bio"]);
    $country = htmlspecialchars($_POST["country"]);
    $birthdate = htmlspecialchars($_POST["birth_date"]);
    $profile_image = $user["profile_image"]; 

    $sql_check = "SELECT id FROM users WHERE username = ? AND id != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("si", $username, $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
        die("The username is already taken.");
    }

    if (!empty($_FILES["profile_image"]["name"])) {
        $target_dir = "./../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); 
        }
    
        $file_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            die("Invalid image format. Use JPG, PNG, or GIF.");
        }
    
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = $target_file;
        } else {
            die("Error uploading the image. Check folder permissions.");
        }
    }

    $sql = "UPDATE users SET username=?, bio=?, country=?, birth_date=?, profile_image=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $username, $bio, $country, $birthdate, $profile_image, $user_id);

    if ($stmt->execute()) {
        header("Location: profile.php?success=1");
        exit();
    } else {
        die("Error updating profile.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Gameverse</title>
    <link rel="stylesheet" href="./../css/profile.css">
    <link rel="shortcut icon" href="./../img/GV.png" type="image/x-icon">
</head>
<body>
    <main>
        <div class="logo">
            <img src="./../img/logo.png" alt="logo">
        </div>
        <section class="profile-form-container">
            <h1>Edit Profile</h1>
            <?php if (!empty($user["profile_image"])): ?>
                <div class="profile-preview-container">
                    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" class="profile-preview" alt="Profile Preview">
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <p class="success-message">Profile updated successfully.</p>
            <?php endif; ?>
            <form action="profile.php" method="POST" enctype="multipart/form-data" class="profile-form">
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    <div id="username-error" class="error-message"></div>
                </div>
                <div class="input-group">
                    <label>Bio</label>
                    <textarea name="bio" required><?php echo htmlspecialchars($user['bio']); ?></textarea>
                    <div id="bio-error" class="error-message"></div>
                </div>
                <div class="input-group">
                    <label>Country</label>
                    <input type="text" name="country" value="<?php echo htmlspecialchars($user['country']); ?>">
                    <div id="country-error" class="error-message"></div>
                </div>
                <div class="input-group">
                    <label>Date of Birth</label>
                    <input type="date" name="birthdate" value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($user['birth_date']))); ?>" required>
                    <div id="birthdate-error" class="error-message"></div>
                </div>
                <div class="input-group">
                    <label>Profile Image</label>
                    <input type="file" name="profile_image">
                </div>
                <button type="submit">Save Changes</button>
                <p class="back-home">
                    <a href="./../index.php">Back to Home</a>
                </p>
            </form>
        </section>
    </main>
    <script src="./../js/profile.js"></script>
</body>
</html>
