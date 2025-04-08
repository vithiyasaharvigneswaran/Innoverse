<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}
require_once 'db.php';

$filter_options = [
    'locations' => [],
    'food_types' => []
];
#######################################################################
$sql_locations = "SELECT DISTINCT location FROM postfood ORDER BY location";
$result_locations = $conn->query($sql_locations);
while($row = $result_locations->fetch_assoc()) {
    $filter_options['locations'][] = $row['location'];
}
#######################################################################
$sql_food_types = "SELECT DISTINCT food_name FROM postfood ORDER BY food_name";
$result_food_types = $conn->query($sql_food_types);
while($row = $result_food_types->fetch_assoc()) {
    $filter_options['food_types'][] = $row['food_name'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Surplus Listings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
</head>
<body>
    <!-- Loading overlay -->
    <div class="loading">
        <div class="loading-content">
            <div class="spinner-border text-primary" role="status"></div>
            <p>Loading data...</p>
        </div>
    </div>

    <!-- Navigation bar to add at the top of index.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <i class="bi bi-box2-heart-fill me-2"></i>Food Surplus
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" 
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">
            <i class="bi bi-house-door-fill"></i> Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="add_food.php">
            <i class="bi bi-plus-circle-fill"></i> Add Food
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="my_claims.php">
            <i class="bi bi-bag-check-fill"></i> My Claims
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
             data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-info-circle-fill"></i> More
          </a>
         
        </li>
      </ul>
      
      <div class="d-flex">
        <div class="dropdown">
          <a class="btn btn-outline-light dropdown-toggle" href="#" role="button" 
             id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle me-1"></i> User
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
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
        <h2 class="mb-4">Available Food Items</h2>
        <?php if(isset($_GET['claimed'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Food item #<?php echo htmlspecialchars($_GET['claimed']); ?> has been claimed successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php
            $error = $_GET['error'];
            switch($error) {
                case 'missing_data':
                    echo 'Please fill in all required fields.';
                    break;
                case 'item_not_found':
                    echo 'The requested food item was not found.';
                    break;
                case 'database_error':
                    echo 'A database error occurred. Please try again.';
                    break;
                default:
                    echo 'An error occurred. Please try again.';
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        

        <div class="row filter-row">
            <div class="col-md-3">
                <label for="location-filter">Location:</label>
                <select id="location-filter" class="form-select">
                    <option value="">All Locations</option>
                    <?php foreach($filter_options['locations'] as $location): ?>
                        <option value="<?php echo htmlspecialchars($location); ?>"><?php echo htmlspecialchars($location); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="food-filter">Food Type:</label>
                <select id="food-filter" class="form-select">
                    <option value="">All Types</option>
                    <?php foreach($filter_options['food_types'] as $food_type): ?>
                        <option value="<?php echo htmlspecialchars($food_type); ?>"><?php echo htmlspecialchars($food_type); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date-filter">Available Before:</label>
                <input type="date" id="date-filter" class="form-control">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button id="reset-filters" class="btn btn-secondary w-100">Reset Filters</button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table id="foodTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Food Name</th>
                        <th>Quantity</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Pickup Time</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="modal fade" id="claimModal" tabindex="-1" aria-labelledby="claimModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="claimModalLabel">Claim Food Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="process_claim.php">
                    <div class="modal-body">
                        <p>You are about to claim: <strong id="food-name-display"></strong></p>
                        <p>Please fill in your details to proceed:</p>
                        
                        <input type="hidden" name="food_id" id="food-id-input">
                        
                        <div class="mb-3">
                            <label for="claimer-name" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="claimer-name" name="claimer_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="claimer-contact" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" id="claimer-contact" name="claimer_contact" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="pickup-time" class="form-label">Preferred Pickup Time</label>
                            <input type="datetime-local" class="form-control" id="pickup-time" name="pickup_time" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="additional-notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="additional-notes" name="additional_notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="claim_food" class="btn btn-primary">Confirm Claim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    
    <script>
    $(document).ready(function() {
        var dataTable = $('#foodTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "services/get_food_data.php",
                type: "POST",
                data: function(d) {
                    d.location = $('#location-filter').val();
                    d.food_type = $('#food-filter').val();
                    d.date = $('#date-filter').val();
                    return d;
                },
                beforeSend: function() {
                    $('.loading').show();
                },
                complete: function() {
                    $('.loading').hide();
                }
            },
            columns: [
                { data: "id", name: "food_id" },
                { data: "name", name: "food_name" },
                { data: "quantity", name: "Qty" },
                { data: "description", name: "Description" },
                { data: "location", name: "location" },
                { data: "pickupTime", name: "pickuptime" },
                { data: "contact", name: "Contactinformation" },
                { 
                    data: "actions", 
                    name: "actions",
                    orderable: false, 
                    searchable: false 
                }
            ],
            order: [[0, 'desc']],
            language: {
                processing: "Loading data. Please wait..."
            }
        });
        
        $('#location-filter, #food-filter, #date-filter').change(function() {
            dataTable.ajax.reload();
        });
        
        // Reset filters
        $('#reset-filters').click(function() {
            $('#location-filter').val('');
            $('#food-filter').val('');
            $('#date-filter').val('');
            dataTable.ajax.reload();
        });
        
        $('#foodTable').on('click', '.claim-btn', function() {
            var food_id = $(this).data('id');
            var food_name = $(this).data('name');
            
            $('#food-id-input').val(food_id);
            $('#food-name-display').text(food_name);
                        
            var now = new Date();
            now.setHours(now.getHours() + 1);
            var dateTimeString = now.toISOString().slice(0, 16);
            $('#pickup-time').val(dateTimeString);
        });
    });
    </script>
</body>
</html>