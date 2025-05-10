<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "SurplusFoodDB";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$sql = "DELETE FROM FoodPosts WHERE id=$id";

$conn->query($sql);
$conn->close();

header("Location: index.php");
exit();
