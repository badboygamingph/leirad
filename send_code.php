<?php
// Include the database connection file
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['phone'])) {
    // Retrieve phone number and sanitize
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // Check if the phone number exists in the database
    $sql = "SELECT * FROM user WHERE phone = ?";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $phone);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // Phone number exists, generate a verification code
                $verificationCode = rand(100000, 999999);

                // Save the code to the database
                $updateSql = "UPDATE user SET verification_code = ? WHERE phone = ?";
                if (mysqli_stmt_prepare($stmt, $updateSql)) {
                    mysqli_stmt_bind_param($stmt, "is", $verificationCode, $phone);
                    if (mysqli_stmt_execute($stmt)) {
                        // Verification code sent successfully, now show a pop-up with the verification code
                        echo "<script>alert('Please copy the verification code: $verificationCode before clicking OK');</script>";
                        // Prompt user to enter the verification code before redirecting
                        echo "<script>
                            window.location.href = 'reset_password.php?phone=$phone&verification_code=$verificationCode';
                        </script>";
                    } else {
                        echo "<script>alert('Error updating verification code: " . mysqli_stmt_error($stmt) . "');</script>";
                    }
                }
            } else {
                echo "<script>alert('Phone number not found.');</script>";
            }
        } else {
            echo "<script>alert('Error: " . mysqli_stmt_error($stmt) . "');</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Verification Code</title>
    <style>
        body {
            font-family: 'Lora', serif;
            background-image: url('img/BG.png');
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 300px;
            margin: 100px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"],
        button {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #6c5ce7;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #4a43f7;
        }
        p {
            text-align: center;
            color: #666;
        }
        a {
            color: #6c5ce7;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reset Password via Phone</h1>
        <form action="" method="POST">
            <input type="text" id="phone" name="phone" placeholder="Phone Number" required>
            <button type="submit">Send Verification Code</button>
            <p>Remembered your password? <a href="index.php">Login</a></p>
            <p>Use an email to reset <a href="forgot.php">Email</a></p>
        </form>
    </div>
</body>
</html>
