<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists in the database (you'll need to implement this part)
    $userExists = checkUserByEmail($email);
    
    if ($userExists) {
        $temporaryPassword = bin2hex(random_bytes(4)); // Generate a random temporary password
        $hashedPassword = password_hash($temporaryPassword, PASSWORD_DEFAULT);

        // Update the temporary password in the database (you'll need to implement this part)
        updateTemporaryPassword($email, $hashedPassword);

        // Send email with the temporary password
        $to = $email;
        $subject = "Temporary Password";
        $message = "Your temporary password is: $temporaryPassword\n\nPlease reset your password after logging in.";
        $headers = "From: no-reply@yourdomain.com";

        if (mail($to, $subject, $message, $headers)) {
            echo "Temporary password sent to your email.";
        } else {
            echo "Failed to send temporary password.";
        }
    } else {
        echo "Email not found.";
    }
}

function checkUserByEmail($email) {
    // Implement your code to check if the email exists in the database
    return true; // Placeholder return value, replace with actual implementation
}

function updateTemporaryPassword($email, $hashedPassword) {
    // Implement your code to update the temporary password in the database
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<body class="container">
    <h2>Forgot Password</h2>
    <form method="POST">
        <input type="email" name="email" class="form-control" placeholder="Email" required><br>
       
        <button type="submit" class="btn btn-success">  <span class="glyphicon glyphicon-send"> </span> Submit</button>
    </form>

    
</body>
</html>
