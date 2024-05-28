<?php
// Start the session
session_start();

// If the user is already logged in, redirect to their respective home page
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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize
    if (
        isset($_POST['username']) && 
        isset($_POST['email']) && 
        isset($_POST['password']) && 
        isset($_POST['user-type']) && 
        isset($_POST['phone'])
    ) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $userType = mysqli_real_escape_string($conn, $_POST['user-type']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);

        // Validate user type
        if (!in_array($userType, ['customer', 'seller', 'admin'])) {
            echo "Invalid user type";
            exit; // Stop further execution
        }

        // Check if the email already exists in the database
        $checkEmailSql = "SELECT * FROM user WHERE email = ?";
        $checkStmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($checkStmt, $checkEmailSql)) {
            // Bind parameter and execute statement
            mysqli_stmt_bind_param($checkStmt, "s", $email);
            mysqli_stmt_execute($checkStmt);
            mysqli_stmt_store_result($checkStmt);

            if (mysqli_stmt_num_rows($checkStmt) > 0) {
                // Email already exists, display error message
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Email already exists',
                                icon: 'error'
                            }).then(function() {
                                    // Redirect back to signup page after 2 seconds
                                    setTimeout(function() {
                                        window.location.href = 'signup.php'; // Change this to the signup page URL
                                    }, 2000);
                                });
                            });
                      </script>";
                exit();
            }
        } else {
            // Error occurred during statement preparation
            echo "Error: " . mysqli_error($conn);
            exit(); // Stop further execution
        }

        // Prepare SQL statement with prepared statement for insertion
        $insertSql = "INSERT INTO user (username, email, password, user_type, phone) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($stmt, $insertSql)) {
            // Bind parameters and execute statement for insertion
            mysqli_stmt_bind_param($stmt, "sssss", $username, $email, $password, $userType, $phone);

            if (mysqli_stmt_execute($stmt)) {
                // Data insertion successful
                $_SESSION['email'] = $email;
                $_SESSION['user_type'] = $userType;

                // Define redirect paths based on user type
                $redirectPaths = [
                    'customer' => 'customer/index.php', // Customer homepage
                    'seller' => 'seller/index.php', // Seller homepage
                    'admin' => 'admin/index.php' // Admin homepage
                ];

                // Check if the user type exists in the redirect paths
                if (array_key_exists($userType, $redirectPaths)) {
                    // Redirect to the appropriate dashboard
                    echo "<script>window.location.href = '{$redirectPaths[$userType]}';</script>";
                    exit();
                } else {
                    // Invalid user type
                    echo "Invalid user type";
                    exit();
                }
            } else {
                // Error occurred during data insertion
                echo "Error: " . mysqli_stmt_error($stmt);
                exit(); // Stop further execution
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            // Error occurred during statement preparation
            echo "Error: " . mysqli_error($conn);
            exit(); // Stop further execution
        }
    } else {
        // Form data not set
        echo "Form submission error";
        exit();
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
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
    <style>
        .logo-icon {
            width: 100px; 
            height: auto; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sign Up</h1>
        <form action="" method="POST">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="email" id="email" name="email" placeholder="Email" required pattern="[a-zA-Z0-9._%+-]+@(gmail|yahoo)\.com" title="Please enter a valid email address with @gmail.com or @yahoo.com">
            <input type="password" id="password" name="password" placeholder="Password" required>
            <input type="text" id="phone" name="phone" placeholder="Phone Number" required pattern="^(09|\+639)\d{9}$" title="Please enter a valid Philippine mobile number (ex. 09*********)">
			<select id="user-type" name="user-type" required>
				<option value="" disabled selected>User Type</option>
				<option value="customer">Customer</option>
				<option value="seller">Seller</option>
				<option value="admin">Admin</option>
			</select>
            <button type="submit">Sign Up</button>
            <p>Already have an account? <a href="index.php">Login</a></p>
        </form>
    </div>
</body>
</html>
