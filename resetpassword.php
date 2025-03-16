<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $temporaryPassword = $_POST['temporary_password'];
    $newPassword = $_POST['new_password'];

    // Check if the email exists and if the temporary password is correct (you'll need to implement this part)
    $isValid = validateTemporaryPassword($email, $temporaryPassword);

    if ($isValid) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the new password in the database (you'll need to implement this part)
        updatePassword($email, $hashedPassword);

        echo "Password has been reset successfully.";
    } else {
        echo "Invalid email or temporary password.";
    }
}

function validateTemporaryPassword($email, $temporaryPassword) {
    // Implement your code to validate the temporary password
    return true; // Placeholder return value, replace with actual implementation
}

function updatePassword($email, $hashedPassword) {
    // Implement your code to update the new password in the database
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Reset Password</h2>
        <form action="reset_password.php" method="post">
            <div class="input-group">
                <i class="icon-envelope"></i>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <i class="icon-lock"></i>
                <input type="password" name="temporary_password" placeholder="Enter temporary password" required>
            </div>
            <div class="input-group">
                <i class="icon-lock"></i>
                <input type="password" name="new_password" placeholder="Enter new password" required>
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>
    </div>
</body>
</html>
