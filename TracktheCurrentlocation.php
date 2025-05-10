<?php
require_once 'db.php';

if (!isset($_GET['food'])) {
    die("Food not specified.");
}

$foodName = $_GET['food'];

$stmt = $conn->prepare("SELECT location, latitude, longitude FROM postfood, reservedfood WHERE postfood.food_name = ? AND postfood.food_id=reservedfood.PostFoodID");
$stmt->bind_param("s", $foodName);
$stmt->execute();
$result = $stmt->get_result();
$food = $result->fetch_assoc();
$stmt->close();

if (!$food || empty($food['latitude']) || empty($food['longitude'])) {
    die("Location not available for this food.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pickup Location</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 500px;
            margin: 20px auto;
            width: 90%;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Pickup Location for: <?= htmlspecialchars($foodName) ?></h2>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const lat = <?= $food['latitude'] ?>;
        const lon = <?= $food['longitude'] ?>;
	<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const lat = <?= $food['latitude'] ?>;
        const lon = <?= $food['longitude'] ?>;

        const map = L.map('map').setView([lat, lon], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        L.marker([lat, lon]).addTo(map)
            .bindPopup("<?= htmlspecialchars($food['location']) ?>")
            .openPopup();
    </script>
</body>
</html>