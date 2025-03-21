<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "SurplusFoodDB";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ""; // Initialize message variable

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["foodName"])) {
    // Validate form inputs
    $food_name = trim($_POST['foodName']);
    $quantity = intval($_POST['quantity']);
    $description = trim($_POST['description']);
    $pickup_location = trim($_POST['pickupLocation']);
    $pickup_time = $_POST['pickupTime'];
    $contact_info = trim($_POST['contactInfo']);

    // Check if required fields are empty
    if (empty($food_name) || empty($quantity) || empty($description) || empty($pickup_location) || empty($pickup_time) || empty($contact_info)) {
        $message = "❌ All fields are required!";
    } else {
        // Prepare SQL statement
        $sql = "INSERT INTO FoodPosts (food_name, quantity, description, pickup_location, pickup_time, contact_info) VALUES (?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sissss", $food_name, $quantity, $description, $pickup_location, $pickup_time, $contact_info);
            
            if ($stmt->execute()) {
                echo "<script>alert('✅ Food details posted successfully!'); window.location.href='index.php';</script>";
                exit;
            } else {
                $message = "❌ Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $message = "❌ Error preparing statement: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Surplus Food</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('food-backgroundaddd.avif') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        h1 {
            color: #0d47a1;
            margin-bottom: 10px;
        }

        h2 {
            color: #1565c0;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 10px;
            text-align: left;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 3px;
            color: #0d47a1;
            font-size: 14px;
        }

        input, textarea {
            width: 95%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        textarea {
            resize: none;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #ff9800;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #e65100;
        }

        .message {
            margin-bottom: 10px;
            font-weight: bold;
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Post Surplus Food</h1>
        <h2>Help reduce food waste by sharing surplus food with those in need</h2>
        <?php if (!empty($message)) { 
            echo '<p class="message">' . htmlspecialchars($message) . '</p>'; 
        } ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="foodName">Food Name:</label>
                <input type="text" id="foodName" name="foodName" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="pickupLocation">Pickup Location:</label>
                <input type="text" id="pickupLocation" name="pickupLocation" required>
            </div>
            <div class="form-group">
                <label for="pickupTime">Pickup Time:</label>
                <input type="datetime-local" id="pickupTime" name="pickupTime" required>
            </div>
            <div class="form-group">
                <label for="contactInfo">Contact Information:</label>
                <input type="text" id="contactInfo" name="contactInfo" required>
            </div>
            <button type="submit">Post Food Details</button>
        </form>
    </div>
</body>
</html>
