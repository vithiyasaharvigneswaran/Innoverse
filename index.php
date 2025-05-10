<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "ecommerce";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all records
$sql = "SELECT * FROM Users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('food-background.jpg') no-repeat center center/cover;
            text-align: center;
            padding: 20px;
            color: white;
        }
        h1 {
            color: #fff;
        }
        .navbar {
            background: rgba(0, 0, 0, 0.8);
            padding: 15px;
            text-align: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-size: 18px;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: rgba(255, 255, 255, 0.9);
            width: 320px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            text-align: left;
            color: black;
        }
        .card h3 {
            color: #ff5722;
        }
        .card p {
            margin: 5px 0;
            color: #333;
        }
        .actions {
            margin-top: 10px;
            text-align: center;
        }
        .actions a {
            text-decoration: none;
            color: white;
            background: #ff5722;
            padding: 8px 15px;
            border-radius: 5px;
            margin: 5px;
            display: inline-block;
            font-weight: bold;
            transition: 0.3s;
        }
        .actions a:hover {
            background: #e64a19;
        }
        .add-food {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background: #ff5722;
            color: white;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
            font-weight: bold;
            transition: 0.3s;
        }
        .add-food:hover {
            background: #e64a19;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <!-- <a href="index.php">Home</a> -->
        <!-- <a href="post_food.php">Post Food</a> -->
        <a href="post_food.php" class="add-food">Add New Food</a>
        <!-- <a href="about.php">About</a>
        <a href="contact.php">Contact</a> -->
    </div>
    <h1>User Dashboard</h1>
    <div class="container">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="card">
                <h3><?= htmlspecialchars($row["name"]) ?></h3>
                <p><strong>Age</strong> <?= $row["age"] ?></p>
                <p><strong>Description:</strong> <?= htmlspecialchars($row["description"]) ?></p>
                
                <div class="actions">
                    <a href="update_food.php?id=<?= $row['id'] ?>">Edit</a>
                    <a href="delete_food.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </div>
            </div>
        <?php } ?>
    </div>
    
    
</body>
</html>

<?php $conn->close(); ?>
