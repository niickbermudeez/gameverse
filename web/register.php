<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Gameverse</title>
    <link rel="stylesheet" href="./../css/register.css">
    <link rel="shortcut icon" href="./../img/GV.png" type="image/x-icon">
</head>
<body>
    <main>
        <div class="logo">
            <img src="./../img/logo.png" alt="logo">
        </div>
        <section class="register-form-container">
            <h1>Create Your Account</h1>

            <?php if (isset($_POST['error'])): ?>
                <div class="error-message">
                    <p><?php echo htmlspecialchars($_POST['error']); ?></p>
                </div>
            <?php endif; ?>

            <form action="./../php/register.php" method="POST" class="register-form">
                <div class="input-group">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" name="first_name" placeholder="Enter your First Name" required>
                </div>
                <div class="input-group">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" name="last_name" placeholder="Enter your Last Name" required>
                </div>
                <div class="input-group">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" placeholder="Enter your age" required>
                </div>
                <div class="input-group">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" placeholder="Enter your country" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your Email" required>
                </div>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a Username" required>
                </div>
                
                <!-- Password con emoticono -->
                <div class="input-group password-container">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter a Password" required>
                    <span id="toggle-password" class="eye-button">👁️</span>
                </div>

                <!-- Confirm Password con emoticono -->
                <div class="input-group password-container">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Re-enter your Password" required>
                    <span id="toggle-confirm-password" class="eye-button">👁️</span>
                </div>

                <button type="submit">Register</button>
            </form>
        </section>
    </main>

    <script src="./../js/register.js"></script>
</body>
</html>
