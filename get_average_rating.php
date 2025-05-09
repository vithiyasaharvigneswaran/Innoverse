<?php
session_start();
require_once 'db.php';

$response = ['success' => false];

if (isset($_SESSION['DonarContactNo'])) {
    $donar_contact = $_SESSION['DonarContactNo'];

    $stmt = $conn->prepare("SELECT AVG(Rating) as avg_rating FROM feedback WHERE DonarContactNo = ?");
    $stmt->bind_param("s", $donar_contact);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $response['success'] = true;
        $response['avg_rating'] = number_format($row['avg_rating'], 1); // 1 decimal
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
