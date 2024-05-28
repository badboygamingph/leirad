<?php
// Start the session
session_start();
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
        <h1>Forgot Password</h1>
        <form action="" method="POST">
            <input type="email" id="email" name="email" placeholder="Email" required>
            <button type="submit">Send Verification Code</button>
        </form>
			<p>Remembered your password? <a href="index.php">Login</a></p>
    </div>
</body>
</html>

<?php
// Include the database connection file
include 'connection.php';

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMAILER/src/PHPMailer.php'; // Path to autoload.php of PHPMailer
require 'PHPMAILER/src/SMTP.php'; // Path to autoload.php of PHPMailer
require 'PHPMAILER/src/Exception.php'; // Path to autoload.php of PHPMailer

// Function to send verification code
function sendVerificationCode($email, $verificationCode) {
    // Set up PHPMailer
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'darielganzon2003@gmail.com';  // Your Gmail email address
    $mail->Password = 'lmmp ntpy yqoq qhpd';  // Your Gmail password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Sender
    $mail->setFrom('darielganzon2003@gmail@gmail.com', 'Leirad Massage'); // Your Gmail email address and your name
    // Recipient
    $mail->addAddress($email);  // Add recipient
    $mail->isHTML(true);
    $mail->Subject = 'Verification Code';
    $mail->Body = 'Your verification code is: ' . $verificationCode;

    // Send email
    if ($mail->send()) {
        return true; // Email sent successfully
    } else {
        return false; // Error sending email
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if the email already exists in the database
    $checkEmailSql = "SELECT * FROM user WHERE email = ?";
    $checkStmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($checkStmt, $checkEmailSql)) {
        // Bind parameter and execute statement
        mysqli_stmt_bind_param($checkStmt, "s", $email);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);

        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            // Email exists, generate a random verification code
            $verificationCode = rand(100000, 999999);

            // Send verification code
            if (sendVerificationCode($email, $verificationCode)) {
                // Verification code sent successfully
                // Store the verification code in the session for later verification
                $_SESSION['email'] = $email;
                $_SESSION['verification_code'] = $verificationCode;

                // Now redirect the user to enter the verification code
                header("Location: enter_verification_code.php");
                exit();
            } else {
                // Error sending verification code
                echo "<script>alert('Error sending verification code. Please try again later.');</script>";
            }
        } else {
            // Email does not exist in the database
            echo "<script>alert('Email does not exist. Please enter a registered email address.');</script>";
        }
    } else {
        // Error occurred during statement preparation
        echo "Error: " . mysqli_error($conn);
    }
}
?>
