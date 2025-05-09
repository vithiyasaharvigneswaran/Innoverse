<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['DonarContactNo'])) {
    echo json_encode(['success' => false]);
    exit;
}


$donar_contact = $_SESSION['DonarContactNo'];
//echo $donar_contact;
$sql = "SELECT Comments FROM feedback WHERE DonarContactNo = ? ORDER BY Date DESC LIMIT 3";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $donar_contact);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row['Comments'];
}

echo json_encode(['success' => true, 'comments' => $comments]);
?>
