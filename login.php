<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT userID, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($userID, $hashed_password);
    
    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        $_SESSION['userID'] = $userID;
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid email or password.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
   
   
</head>
<body class="container1">
    <img src="images/logo.png" class="imagecenter" alt="Center Image" class="center-image" width="20%" height="20%">

    <h2>Login</h2>
    <form method="POST">
        <input type="email" name="email" class="form-control" placeholder="Email" required><br>
        <input type="password" name="password" class="form-control" placeholder="Password" required><br>
        <button type="submit" class="btn btn-success"> <span class="glyphicon glyphicon-log-in"></span> Login</button> <br><br> <a href="forgotpassword.php" class="btn btn-success">Forgot Password?</a>
    </form>

    
</body>
</html>
