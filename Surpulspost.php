<?php
// Enable error reporting
error_reporting(E_ALL);
//ini_set('display_errors', 1);

// Database connection
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "surpulsfood"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $foodName = $_POST['foodName'];
    $quantity = intval($_POST['quantity']); // Ensure it's an integer
    $description = $_POST['description'];
    $pickupLocation = $_POST['pickupLocation'];
    $pickupTime = $_POST['pickupTime'];
    $contactInfo = $_POST['contactInfo'];

    // Debugging: Print received data


    // Validate required fields
    if (empty($foodName) || empty($quantity) || empty($pickupLocation) || empty($pickupTime) || empty($contactInfo)) {
        die("All fields are required!");
    }

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO postfood (food_name, Qty, Description, location, pickuptime, Contactinformation) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sissss", $foodName, $quantity, $description, $pickupLocation, $pickupTime, $contactInfo);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script> alert('Food details posted successfully!')</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
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
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        input[type="datetime-local"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Post Surplus Food Details</h1>
        <form id="foodForm" method="POST" >
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
                <textarea id="description" name="description" rows="4" required></textarea>
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