<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Fetch all food posts
$sql = "SELECT * FROM postfood ORDER BY pickuptime ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Food Posts</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #45a049;
            --light-gray: #f5f5f5;
            --medium-gray: #e0e0e0;
            --dark-gray: #757575;
            --white: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
            color: var(--primary-color);
            font-weight: 600;
        }

        .post-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .food-card {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s;
        }

        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .food-name {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .food-detail {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .food-detail i {
            margin-right: 10px;
            color: var(--dark-gray);
            width: 20px;
            text-align: center;
        }

        .action-buttons {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        .btn {
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-secondary {
            background-color: var(--medium-gray);
            color: #333;
        }

        .btn-secondary:hover {
            background-color: #d0d0d0;
        }

        .empty-message {
            text-align: center;
            grid-column: 1 / -1;
            padding: 40px;
            color: var(--dark-gray);
        }

        @media (max-width: 768px) {
            .post-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Available Food Posts</h1>
        
        <div class="post-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="food-card">
                        <div class="food-name"><?php echo htmlspecialchars($row['food_name']); ?></div>
                        
                        <div class="food-detail">
                            <i class="fas fa-utensils"></i>
                            <span><?php echo htmlspecialchars($row['Description']); ?></span>
                        </div>
                        
                        <div class="food-detail">
                            <i class="fas fa-boxes"></i>
                            <span>Quantity: <?php echo htmlspecialchars($row['Qty']); ?> servings</span>
                        </div>
                        
                        <div class="food-detail">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($row['location']); ?></span>
                        </div>
                        
                        <div class="food-detail">
                            <i class="fas fa-clock"></i>
                            <span>Pickup by: <?php echo date('M j, Y g:i A', strtotime($row['pickuptime'])); ?></span>
                        </div>
                        
                        <div class="food-detail">
                            <i class="fas fa-phone-alt"></i>
                            <span><?php echo htmlspecialchars($row['Contactinformation']); ?></span>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="javascript:void(0);" onclick="shareOnFacebook(
							'<?php echo htmlspecialchars($row['food_name']); ?>', 
							'<?php echo htmlspecialchars($row['Description']); ?>',
							'<?php echo htmlspecialchars($row['location']); ?>',
							'<?php echo date('M j, Y g:i A', strtotime($row['pickuptime'])); ?>'
							)" class="btn btn-secondary">
							<i class="fas fa-share-alt"></i> <i class="fab fa-facebook"></i>
						</a>
                            <a href="#" class="btn btn-primary">
                                <i class="fas fa-check"></i> Reserve
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-message">
                    <i class="fas fa-box-open fa-3x" style="margin-bottom: 15px;"></i>
                    <h3>No food posts available</h3>
                    <p>Check back later or post some food yourself!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
	
	<script>
    function shareOnFacebook(foodName, description, location, pickupTime) {
        // Create the share text
        var shareText = "Check out this food available for pickup:\n\n";
        shareText += "Food: " + foodName + "\n";
        shareText += "Description: " + description + "\n";
        shareText += "Location: " + location + "\n";
        shareText += "Pickup by: " + pickupTime + "\n\n";
        shareText += "Help reduce food waste by sharing surplus food!";
        
        // URL to share (replace with your actual URL if you have one)
        var shareUrl = window.location.href;
		
		// Facebook share URL
        var facebookShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + 
                              encodeURIComponent(shareUrl) + 
                              '&quote=' + encodeURIComponent(shareText);
        
        // Open the share dialog in a new window
        window.open(facebookShareUrl, 'facebook-share-dialog', 
                   'width=626,height=436,top=100,left=100');
    }
    </script>
	

    <?php $conn->close(); ?>
</body>
</html>