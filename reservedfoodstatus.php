<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['ReserverName']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once 'db.php';

// Define filter options for status
$statuses = ['Pending', 'Collected'];

// Function to generate the report based on the status
function generateReport($status) {
    global $conn;

    // Query to fetch data from the 'reservedfood' table based on status
    $sql = "SELECT Id, ReserverName, FoodName, Qty, DonarContactNo, ReserverContact, ReservedDate, Status, PostFoodID
            FROM reservedfood
            WHERE Status = '$status'";

    $result = $conn->query($sql);

    // Initialize summary variables
    $totalQty = 0;
    $totalRecords = 0;

    // Check if results exist
    if ($result->num_rows > 0) {
      
        echo "<h3>Status: $status</h3>";
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead><tr><th>ID</th><th>Reserver Name</th><th>Food Name</th><th>Quantity</th><th>Donor Contact</th><th>Reserver Contact</th><th>Reserved Date</th><th>Post Food ID</th></tr></thead><tbody>";
        
        // Loop through the results and display them
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["Id"] . "</td>
                    <td>" . $row["ReserverName"] . "</td>
                    <td>" . $row["FoodName"] . "</td>
                    <td>" . $row["Qty"] . "</td>
                    <td>" . $row["DonarContactNo"] . "</td>
                    <td>" . $row["ReserverContact"] . "</td>
                    <td>" . $row["ReservedDate"] . "</td>
                    <td>" . $row["PostFoodID"] . "</td>
                  </tr>";
            
            // Update the summary data
            $totalQty += $row["Qty"];
            $totalRecords++;
        }

        echo "</tbody></table>";
        echo "<b>Summary for Status: $status</b><br>";
        echo "Total Records: $totalRecords<br>";
        echo "Total Quantity: $totalQty<br>";
        exit();
    } else {
        echo "<p>No records found for status: $status</p>";
    }
}

// Process the AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];
    // Check if status is valid
    if (in_array($status, $statuses)) {
        generateReport($status);
    } else {
        echo "<p>Invalid status selected. Please try again.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reserved Food Status Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-box2-heart-fill me-2"></i>Food Surplus</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-house-door-fill"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="Updatepostfood.php"><i class="bi bi-plus-circle-fill"></i> Add Food</a></li>
                <li class="nav-item"><a class="nav-link" href="View.php"><i class="bi bi-eye-fill"></i> View Food</a></li>
                <li class="nav-item"><a class="nav-link" href="myclaims.php"><i class="bi bi-bag-check-fill"></i> My Claims</a></li>
            </ul>
            <div class="d-flex">
                <div class="dropdown">
                    <a class="btn btn-outline-light dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i> <?php echo $_SESSION['ReserverName']."-".$_SESSION['role']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> My Profile</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="mb-4">Reserved Food Status Report</h1>

    <!-- Status Dropdown -->
    <div class="mb-3">
        <label for="status-select" class="form-label">Select Status:</label>
        <select id="status-select" class="form-select" onchange="loadReport()">
            <option value="">--Select Status--</option>
            <option value="Pending">Pending</option>
            <option value="Collected">Collected</option>
        </select>
    </div>

    <!-- Report Content (Dynamically updated) -->
    <div id="report-content"></div>

    <!-- Print Button (Initially hidden) -->
    <button id="print-btn" class="btn btn-primary mt-3" style="display:none;" onclick="printReport()">Print Report</button>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Function to load the report based on selected status
function loadReport() {
    const status = document.getElementById('status-select').value;
    
    if (status) {
        const formData = new FormData();
        formData.append('status', status);

        fetch('reservedfoodstatus.php', {
            method: 'POST',
            body: formData,
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache',
                'Expires': '0'
            }
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('report-content').innerHTML = data;
            const printButton = document.getElementById('print-btn');
            if (data.trim() === '' || data.includes('No records found')) {
                printButton.style.display = 'none';
            } else {
                printButton.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('report-content').innerHTML = '<p>Error loading the report. Please try again.</p>';
            document.getElementById('print-btn').style.display = 'none';
        });
    } else {
        document.getElementById('report-content').innerHTML = '';
        document.getElementById('print-btn').style.display = 'none';
    }
}


// Function to print the report
function printReport() {
    const printContent = document.getElementById('report-content').innerHTML;
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print Report</title></head><body>');
    printWindow.document.write(printContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
   
    printWindow.print();
}
</script>

</body>
</html>