<?php
session_start();
include 'db.php';

// Redirect if no reset session exists
if (!isset($_SESSION['email'])) {
    echo "<div class='alert alert-danger'>Unauthorized access. Please use the link sent to your email.</div>";
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $message = "<div class='alert alert-danger'>Passwords do not match.</div>";
    } else {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $email = $_SESSION['email'];

        //$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt = $conn->prepare("UPDATE users SET password = ?, is_temp_password = 0 WHERE email = ?");

        $stmt->bind_param("ss", $hashed, $email);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Password changed successfully. <a href='login.php'>Login Now</a></div>";
            unset($_SESSION['email']); // clear session after reset
        } else {
            $message = "<div class='alert alert-danger'>Failed to update password. Please try again.</div>";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
 
</head>
<body class="container1" style="margin-top: 50px;">
<img src="images/logo.png" class="imagecenter" alt="Center Image" width="20%" height="20%">
    <h2>Change Password</h2>
    <?php if (!empty($message)) echo $message; ?>
    <form method="POST" class="form-group">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" class="form-control" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" class="form-control" required><br>

        <button type="submit" class="btn btn-success">
            <span class="glyphicon glyphicon-refresh"></span> Change Password
        </button>
    </form>
</body>
</html>
