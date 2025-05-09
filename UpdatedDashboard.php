<?php
session_start();

if (!isset($_SESSION['ReserverName'])) {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

$filter_options = [
    'locations' => [],
    'food_types' => []
];

// Fetch Locations
$sql_locations = "SELECT DISTINCT location FROM postfood WHERE status = 'Available' ORDER BY location";
$result_locations = $conn->query($sql_locations);
while($row = $result_locations->fetch_assoc()) {
    $filter_options['locations'][] = $row['location'];
}

// Fetch Food Types
$sql_food_types = "SELECT DISTINCT food_name FROM postfood WHERE status = 'Available' ORDER BY food_name";
$result_food_types = $conn->query($sql_food_types);
while($row = $result_food_types->fetch_assoc()) {
    $filter_options['food_types'][] = $row['food_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Surplus Listings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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

<!-- Claim Success Alert -->
<div class="container mt-3">
    <div id="claim-alert" class="alert alert-success alert-dismissible fade show d-none" role="alert">
        <span id="claim-alert-msg">Claim successful!</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>

<!-- Main Content -->
<div class="container mt-5">
    <h2 class="mb-4">Available Food Items</h2>

    <!-- Filters -->
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

    <!-- Food Table -->
    <div class="table-responsive mt-4">
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
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Claim Modal -->
<div class="modal fade" id="claimModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="claimForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Claim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="post_id" id="modal-post-id">
                    <input type="hidden" name="donar_contact" id="modal-donar-contact">

                    <div class="mb-3">
                        <label>Food Name</label>
                        <input type="text" class="form-control" name="food_name" id="modal-food-name" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Quantity</label>
                        <input type="text" class="form-control" name="qty" id="modal-qty" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Donor Contact</label>
                        <input type="text" class="form-control" id="modal-donar-visible" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Your Name</label>
                        <input type="text" class="form-control" name="reserver_name" value="<?php echo htmlspecialchars($_SESSION['ReserverName']); ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Your Contact</label>
                        <input type="text" class="form-control" name="reserver_contact" value="<?php echo htmlspecialchars($_SESSION['ReserverContact']); ?>" readonly>
                    </div>

                    <div class="mb-3" id="rating-display" style="margin-top: 10px; font-size: 16px;">
                        <!-- Donor Average Rating Will Appear Here -->
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Confirm Claim</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal (for posters) -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
      <form id="editForm">
        <div class="modal-content">
          <div class="modal-header">
            <h5>Edit Food Post</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="post_id" id="edit-post-id">
            <div class="mb-3">
              <label>Food Name</label>
              <input type="text" readonly id="edit-food-name" class="form-control">
            </div>
            <div class="mb-3">
              <label>Quantity</label>
              <input type="number" name="qty" id="edit-qty" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Description</label>
              <textarea name="description" id="edit-description" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label>Location</label>
              <input type="text" name="location" id="edit-location" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Pickup Time</label>
              <input type="datetime-local" name="pickup_time" id="edit-pickuptime" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary">Update</button>
          </div>
        </div>
      </form>
    </div>
  </div>

<!-- JavaScript Files -->
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
            beforeSend: function() { $('.loading').show(); },
            complete: function() { $('.loading').hide(); }
        },
        columns: [
            { data: "id" },
            { data: "name" },
            { data: "quantity" },
            { data: "description" },
            { data: "location" },
            { data: "pickupTime" },
            { data: "contact" },
            { data: "actions", orderable: false, searchable: false }
        ]
    });

    $('#location-filter, #food-filter, #date-filter').change(function() {
        dataTable.ajax.reload();
    });

    $('#reset-filters').click(function() {
        $('#location-filter, #food-filter').val('');
        $('#date-filter').val('');
        dataTable.ajax.reload();
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('claim-btn')) {
            const btn = e.target;
            const donarContact = btn.dataset.contact;

            $('#modal-post-id').val(btn.dataset.id);
            $('#modal-food-name').val(btn.dataset.name);
            $('#modal-qty').val(btn.dataset.qty);
            $('#modal-donar-contact').val(donarContact);
            $('#modal-donar-visible').val(donarContact);

            $('#rating-display').html('Loading donor rating...');
            $('#feedback-comments').hide();
            $('#comments-list').empty();

            fetch('set_session.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'donar_contact=' + encodeURIComponent(donarContact)
            })
            .then(response => response.text())
            .then(sessionResult => {
                if (sessionResult.trim() === 'success') {
                    fetch('get_average_rating.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const avg = parseFloat(data.avg_rating);
                            let starsHtml = '';
                            for (let i = 1; i <= 5; i++) {
                                if (avg >= i) {
                                    starsHtml += '<i class="bi bi-star-fill text-warning"></i>';
                                } else if (avg >= (i - 0.5)) {
                                    starsHtml += '<i class="bi bi-star-half text-warning"></i>';
                                } else {
                                    starsHtml += '<i class="bi bi-star text-warning"></i>';
                                }
                            }
                            $('#rating-display').html(`
                                <strong>Donor Average Rating:</strong> ${avg.toFixed(1)}<br>${starsHtml}
                                <br><button type="button" id="load-comments" class="btn btn-link p-0">Latest Feedback</button>
                                <div id='feedback-comments'><div id='comments-list'></div></div>
                            `);

                            $('#load-comments').click(function() {
                                fetch('get_feedback_comments.php')
                                .then(res => res.json())
                                .then(feedback => {
                                    if (feedback.success) {
                                        $('#feedback-comments').show();
                                        feedback.comments.forEach(comment => {
                                            $('#comments-list').append(`<li class="list-group-item">${comment}</li>`);
                                        });
                                        $("#load-comments").hide();
                                    }
                                });
                            });

                        } else {
                            $('#rating-display').html('<strong>Donor Average Rating:</strong> No rating yet.');
                        }
                    });
                }
            });

            new bootstrap.Modal(document.getElementById('claimModal')).show();
        }
    });

    $('#claimForm').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('reserve.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(data => {
            if (data.trim() === 'success') {
                bootstrap.Modal.getInstance(document.getElementById('claimModal')).hide();
                $('#claim-alert').removeClass('d-none');
                setTimeout(() => {
                    $('#claim-alert').addClass('d-none');
                    location.reload();
                }, 3000);
            }
        });
    });
    //--------------------
    // Edit
    $(document).on('click','.edit-btn',function(){
      $('#edit-post-id').val(this.dataset.id);
      $('#edit-food-name').val(this.dataset.name);
      $('#edit-qty').val(this.dataset.qty);
      $('#edit-description').val(this.dataset.description);
      $('#edit-location').val(this.dataset.location);
      $('#edit-pickuptime').val(this.dataset.pickuptime.replace(' ','T').slice(0,16));
      new bootstrap.Modal($('#editModal')).show();
    });
    $('#editForm').submit(function(e){
      e.preventDefault();
      fetch('services/update_food_post.php',{
        method:'POST', body:new FormData(this)
      }).then(r=>r.text()).then(txt=>{
        if(txt.trim()==='success'){
          bootstrap.Modal.getInstance($('#editModal')).hide();
          dataTable.ajax.reload();
        } else {
          alert('Update failed: '+txt);
        }
      });
    });
    
    //------------------
});
</script>

</body>
</html>
