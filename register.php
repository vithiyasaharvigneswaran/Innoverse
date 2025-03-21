<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $phone = htmlspecialchars($_POST['phone']);
    $location = htmlspecialchars($_POST['location']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (name, phone, location, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $phone, $location, $email, $password);

    if ($stmt->execute()) {
        echo "Registration successful! <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    
</head>
<body class="container">
<img src="images/logo1.png" class="imagecenter" alt="Center Image" class="center-image" width="20%" height="20%">

    <h2>Register</h2>
    <form method="POST">
        <input type="text" name="name" class="form-control" placeholder="Name" required><br>
        <input type="text" name="phone" class="form-control" placeholder="Phone" required><br>
        <input type="text" name="location" class="form-control" placeholder="Location" required><br>
        <input type="email" name="email" class="form-control" placeholder="Email" required><br>
        <input type="password" name="password" class="form-control" placeholder="Password" required><br>
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-log-in"> Register</button>
    </form>
</body>
</html>
