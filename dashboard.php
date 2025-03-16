<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Surplus Food Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container">
    <h2>Welcome to Surplus Food Management System</h2>
    <a href="logout.php" class="btn btn-danger">Logout</a>
    <h2>Available Food</h2>
        <table class="table">
            <tr>
                <th>Food Item</th>
                <th>Quantity/Parcel</th>
                <th>Veg/ Non-Veg</th>
                <th>Location</th>
                <th>Action</th>
            </tr>
            <tr>
                <td>Rice and Curry</td>
                <td>10</td>
                <td>Veg</td>
                <td>Thivya Mahal</td>
                <td><a href="#" class="btn btn-primary"> <span class="glyphicon glyphicon-ok-circle"></span> Claim</a></td>
            </tr>
            <tr>
                <td>Rice and Curry</td>
                <td>15</td>
                <td>Non-Veg</td>
                <td>Selva Mahal</td>
                <td><a href="#" class="btn btn-primary"> <span class="glyphicon glyphicon-ok-circle"></span> Claim</a></td>
            </tr>
        </table>
</body>
</html>
