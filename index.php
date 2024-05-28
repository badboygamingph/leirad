<?php
// Start the session
session_start();

// If user is already logged in, redirect to their respective home page
if (isset($_SESSION['email']) && isset($_SESSION['user_type'])) {
    switch ($_SESSION['user_type']) {
        case "customer":
            header("Location: customer/index.php");
            exit;
        case "seller":
            header("Location: seller/index.php");
            exit;
        case "admin":
            header("Location: admin/index.php");
            exit;
    }
}

// Include the database connection file
include 'connection.php';

// Initialize message variable
$message = "";
$loginSuccess = false; // Track login success
$redirectUrl = ""; // Initialize redirect URL

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $userType = mysqli_real_escape_string($conn, $_POST['user-type']);

    // Validate user type
    if (!in_array($userType, ['customer', 'seller', 'admin'])) {
        $message = "Invalid user type";
    } else {
        // Prepare SQL statement with prepared statement
        $sql = "SELECT * FROM user WHERE BINARY email = ? AND BINARY password = ? AND BINARY user_type = ?";
        $stmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($stmt, $sql)) {
            // Bind parameters and execute statement
            mysqli_stmt_bind_param($stmt, "sss", $email, $password, $userType);

            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Set session variables
                    $_SESSION['email'] = $email;
                    $_SESSION['user_type'] = $userType;

                    // Set message for successful login
                    $message = "Login successful. Redirecting...";
                    $loginSuccess = true; // Mark login as successful

                    // Set the redirect URL based on user type
                    switch ($userType) {
                        case "customer":
                            $redirectUrl = "customer/index.php";
                            break;
                        case "seller":
                            $redirectUrl = "seller/index.php";
                            break;
                        case "admin":
                            $redirectUrl = "admin/index.php";
                            break;
                    }
                } else {
                    // Invalid email, password, or user type
                    $message = "Invalid email, password, or user type";
                }
            } else {
                // Error occurred during statement execution
                $message = "Error: " . mysqli_stmt_error($stmt);
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            // Error occurred during statement preparation
            $message = "Error: " . mysqli_error($conn);
        }
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .logo-icon {
            width: 100px;
            height: auto; 
        }
        
        /* Styled message */
        .message {
            display: none; /* Hide by default */
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <audio id="welcomeAudio" autoplay style="display: none;">
        <source src="audio.mp3" type="audio/mpeg">
    </audio>

    <div class="container">
<h1>Login</h1>
<div class="message"><?php echo $message; ?></div> <!-- Display PHP message -->
<form action="" method="POST">
<input type="email" id="email" name="email" placeholder="Email" required>
<input type="password" id="password" name="password" placeholder="Password" required>
<select id="user-type" name="user-type" required>
<option value="" disabled selected>User Type</option>
<option value="customer">Customer</option>
<option value="seller">Seller</option>
<option value="admin">Admin</option>
</select>
<button type="submit">Login</button>
<p><a href="forgot.php">Forgot password?</a></p>
<p>Don't have an account? <a href="signup.php">Sign up</a></p>
</form>

