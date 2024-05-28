<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Retrieve email and verification code from session variables
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
    $verificationCode = isset($_SESSION['verification_code']) ? $_SESSION['verification_code'] : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
	 <div class="dar-text">
            <span>Leirad Massage</span>
            <span class="dar-text-sub">| Booking & Services</span>
        </div>
	<style>
        .logo-icon {
            width: 100px;
            height: auto; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Enter Verification Code</h1>
        <form action="reset_password.php" method="POST">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="hidden" name="code" value="<?= htmlspecialchars($verificationCode) ?>">
            <input type="text" id="verification_code" name="verification_code" placeholder="Verification Code" required>
            <input type="password" id="password" name="password" placeholder="New Password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
