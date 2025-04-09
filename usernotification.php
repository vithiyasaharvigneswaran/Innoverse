<?php
require_once 'vendor/autoload.php'; // Load Twilio SDK

use Twilio\Rest\Client;

// Twilio credentials from your Twilio Console
$sid    = "AC2911b31aa4bed132847b25ead739de08";
$token  = "45c953178f5af32b6c2a5ee9e820fe0c";
$twilio = new Client($sid, $token);

// Database connection
require_once 'db.php';
// Twilio-verified phone number
$twilio_number = '++19404488901'; 


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 1: Get all unclaimed food items
$foodResult = $conn->query("SELECT food_name, Qty FROM postfood WHERE status = 'Not claimed'");

$foodList = [];
if ($foodResult->num_rows > 0) {
    while ($row = $foodResult->fetch_assoc()) {
        $foodList[] = $row['Qty'] . ' qty x ' . $row['food_name'];
    }
}

if (empty($foodList)) {
    die("No unclaimed food to notify about.");
}

$messageBody = "Unclaimed food available: " . implode(', ', $foodList) . ". Please claim if needed.";

// Step 2: Get all user phone numbers
$userResult = $conn->query("SELECT phone FROM users");
echo  "<h3>SMS Sending Status for the Unclaimed food to the registered User</h3><br>";
if ($userResult->num_rows > 0) {
    while ($user = $userResult->fetch_assoc()) {
        $phone = $user['phone']; // E.164 format e.g., +94761234567

        try {
            $twilio->messages->create(
                $phone,
                [
                    'from' => $twilio_number,
                    'body' => $messageBody
                ]
            );
            echo "✅ SMS sent to $phone<br>";
        } catch (Exception $e) {
            echo "❌ Failed to send to $phone: " . $e->getMessage() . "<br>";
        }
    }
} else {
    echo "No users found.";
}
echo "<br><h4>Summary of the SMS is : ". $messageBody ."</h4>";
$conn->close();

?>