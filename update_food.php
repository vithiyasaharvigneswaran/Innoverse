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

$message = "";
$food_id = "";
$food_name = "";
$quantity = "";
$description = "";
$pickup_location = "";
$pickup_time = "";
$contact_info = "";

// Retrieve data for updating
if (isset($_GET['id'])) {
    $food_id = $_GET['id'];
    $sql = "SELECT * FROM FoodPosts WHERE id=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $food_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $food_name = $row['food_name'];
            $quantity = $row['quantity'];
            $description = $row['description'];
            $pickup_location = $row['pickup_location'];
            $pickup_time = $row['pickup_time'];
            $contact_info = $row['contact_info'];
        }
        $stmt->close();
    }
}

// Insert or update record
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $food_id = $_POST['foodId'];
    $food_name = trim($_POST['foodName']);
    $quantity = intval($_POST['quantity']);
    $description = trim($_POST['description']);
    $pickup_location = trim($_POST['pickupLocation']);
    $pickup_time = $_POST['pickupTime'];
    $contact_info = trim($_POST['contactInfo']);

    if (empty($food_name) || empty($quantity) || empty($description) || empty($pickup_location) || empty($pickup_time) || empty($contact_info)) {
        $message = "❌ All fields are required!";
    } else {
        if (!empty($food_id)) {
            // Update existing record
            $sql = "UPDATE FoodPosts SET food_name=?, quantity=?, description=?, pickup_location=?, pickup_time=?, contact_info=? WHERE id=?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sissssi", $food_name, $quantity, $description, $pickup_location, $pickup_time, $contact_info, $food_id);
                if ($stmt->execute()) {
                    echo "<script>alert('✅ Food details updated successfully!'); window.location.href='index.php';</script>";
                exit;
                } else {
                    $message = "❌ Error: " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            // Insert new record
            $sql = "INSERT INTO FoodPosts (food_name, quantity, description, pickup_location, pickup_time, contact_info) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sissss", $food_name, $quantity, $description, $pickup_location, $pickup_time, $contact_info);
                if ($stmt->execute()) {
                    $message = "✅ Food details posted successfully!";
                } else {
                    $message = "❌ Error: " . $stmt->error;
                }
                $stmt->close();
            }
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
    <title>Manage Surplus Food</title>
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

        .table-container {
            margin-top: 20px;
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Surplus Food</h1>
        <?php if (!empty($message)) { echo '<p class="message">' . htmlspecialchars($message) . '</p>'; } ?>
        <form method="POST">
            <input type="hidden" name="foodId" value="<?php echo htmlspecialchars($food_id); ?>">
            <div class="form-group">
                <label>Food Name:</label>
                <input type="text" name="foodName" value="<?php echo htmlspecialchars($food_name); ?>" required>
            </div>
            <div class="form-group">
                <label>Quantity:</label>
                <input type="number" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>" required>
            </div>
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="form-group">
                <label>Pickup Location:</label>
                <input type="text" name="pickupLocation" value="<?php echo htmlspecialchars($pickup_location); ?>" required>
            </div>
            <div class="form-group">
                <label>Pickup Time:</label>
                <input type="datetime-local" name="pickupTime" value="<?php echo htmlspecialchars($pickup_time); ?>" required>
            </div>
            <div class="form-group">
                <label>Contact Info:</label>
                <input type="text" name="contactInfo" value="<?php echo htmlspecialchars($contact_info); ?>" required>
            </div>
            <button type="submit">Update Food Details</button>
        </form>
    </div>
</body>
</html>
