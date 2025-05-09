<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['donar_contact'])) {
    echo json_encode(['success' => false, 'message' => 'No donor selected.']);
    exit;
}

$donarContact = $_SESSION['donar_contact'];

$sql = "SELECT comment FROM ratings WHERE donar_contact = ? ORDER BY id DESC LIMIT 3";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $donarContact);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while($row = $result->fetch_assoc()) {
    $comments[] = ['comment' => $row['comment']];
}

echo json_encode(['success' => true, 'comments' => $comments]);
?>