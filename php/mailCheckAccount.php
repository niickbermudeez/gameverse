<?php
include 'config.php';

if (isset($_GET['code']) && isset($_GET['mail'])) {
    $code = $_GET['code'];
    $email = $_GET['mail'];

    $sql = "SELECT id FROM users WHERE email = ? AND activationCode = ? AND active = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $updateSql = "UPDATE users SET active = 1, activationDate = NOW() WHERE email = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("s", $email);

        if ($updateStmt->execute()) {
            header("Location: ./../web/activation_result.html?success=Account activated successfully!");
            exit();
        } else {
            header("Location: ./../web/activation_result.html?error=Activation failed.");
            exit();
        }
    } else {
        header("Location: ./../web/activation_result.html?error=Invalid activation link.");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ./../web/activation_result.html?error=Invalid request.");
    exit();
}
?>
