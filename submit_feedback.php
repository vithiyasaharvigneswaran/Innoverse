
<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "fooddonationdb";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['userId'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];
    $date = date('Y-m-d');

    $sql = "INSERT INTO Feedback (UserID, Rating, Comments, Date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $userId, $rating, $comments, $date);
    
    if ($stmt->execute()) {
        echo "Feedback submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>