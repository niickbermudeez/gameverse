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

// Inserir publicació a la bbdd
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION["user_id"];
    $textDescription = trim($_POST["text_description"]);
    $imagePath = null;

    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "./../uploads/";
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Publication - Gameverse</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Font Awesome para íconos adicionales -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="shortcut icon" href="./../img/GV.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/create-publication.css">
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
                        <a class="nav-link" href="./community.php">
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
                        <h1 class="hero-title">
                            Create Publication
                        </h1>
                        <p class="hero-subtitle">Share your gaming moments with the community</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Create Publication Form Section -->
        <section class="section-padding">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-10">
                        <div class="publication-card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="bi bi-controller"></i>
                                    What's on your mind, gamer?
                                </h3>
                            </div>
                            
                            <form action="create-publication.php" method="POST" enctype="multipart/form-data" class="publication-form">
                                <div class="form-group">
                                    <label for="text_description" class="form-label">
                                        <i class="bi bi-pencil-square"></i> Description
                                    </label>
                                    <textarea 
                                        name="text_description" 
                                        id="text_description"
                                        class="form-control custom-textarea" 
                                        placeholder="Share your gaming experience, tips, or thoughts with the community..."
                                        rows="6"
                                        required
                                    ></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="image" class="form-label">
                                        <i class="bi bi-image"></i> Upload Image (Optional)
                                    </label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="image" id="image" accept="image/*" class="file-input">
                                        <label for="image" class="file-upload-label">
                                            <i class="bi bi-cloud-upload"></i>
                                            Choose an image or drag & drop here
                                        </label>
                                        <div class="file-preview" id="imagePreview"></div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> 
                                    </button>
                                </div>
                            </form>
                        </div>
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
                    <p>Share your gaming passion with the world.</p>
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

        // File upload preview
        const fileInput = document.getElementById('image');
        const filePreview = document.getElementById('imagePreview');
        const fileLabel = document.querySelector('.file-upload-label');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    filePreview.innerHTML = `
                        <div class="preview-container">
                            <img src="${e.target.result}" alt="Preview" class="preview-image">
                            <button type="button" class="remove-image" onclick="removeImage()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                    fileLabel.innerHTML = `<i class="bi bi-check-square"></i> Image selected: ${file.name}`;
                    fileLabel.classList.add('file-selected');
                };
                reader.readAsDataURL(file);
            }
        });

        function removeImage() {
            fileInput.value = '';
            filePreview.innerHTML = '';
            fileLabel.innerHTML = '<i class="fas fa-cloud-upload-alt me-2"></i>Choose an image or drag & drop here';
            fileLabel.classList.remove('file-selected');
        }

        // Drag and drop functionality
        const fileUploadWrapper = document.querySelector('.file-upload-wrapper');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileUploadWrapper.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            fileUploadWrapper.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileUploadWrapper.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            fileUploadWrapper.classList.add('drag-over');
        }

        function unhighlight(e) {
            fileUploadWrapper.classList.remove('drag-over');
        }

        fileUploadWrapper.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                fileInput.files = files;
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);
            }
        }

        // Textarea auto-resize
        const textarea = document.getElementById('text_description');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    </script>
</body>
</html>