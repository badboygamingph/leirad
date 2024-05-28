<?php
// Include the database connection file
include 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $verificationCode = mysqli_real_escape_string($conn, $_POST['code']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['password']);
    $enteredCode = mysqli_real_escape_string($conn, $_POST['verification_code']);

    // Check if the verification code matches
    if ($verificationCode !== $enteredCode) {
        echo "<script>alert('Invalid verification code. Please try again.');
              window.location.href = 'enter_verification_code.php';</script>"; // Redirect to enter verification code page
        exit();
    }

    // Update the password in the database
    $updateSql = "UPDATE user SET password = ? WHERE email = ?";
    $updateStmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($updateStmt, $updateSql)) {
        // Bind parameters and execute statement for updating password
        mysqli_stmt_bind_param($updateStmt, "ss", $newPassword, $email);

        if (mysqli_stmt_execute($updateStmt)) {
            // Password updated successfully
            echo "<script>alert('Password updated successfully.');
                  window.location.href = 'index.php';</script>"; // Redirect to login page
            exit();
        } else {
            // Error occurred during password update
            echo "<script>alert('Error updating password. Please try again later.');
                  window.location.href = 'enter_verification_code.php';</script>"; // Redirect to enter verification code page
            exit();
        }

        // Close statement
        mysqli_stmt_close($updateStmt);
    } else {
        // Error occurred during statement preparation
        echo "Error: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}
?>
