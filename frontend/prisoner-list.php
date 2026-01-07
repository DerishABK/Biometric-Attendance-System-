<?php
require_once '../backend/session_start.php';
include '../backend/db_connect.php';

// Fetch prisoners from database
$sql = "SELECT prisoner_id, full_name, cell_number, crime FROM prisoners ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Warden Dashboard – Prisoner List</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    body {
      min-height: 100vh;
      background: radial-gradient(circle at top left, #1e3a8a, #050505 60%);
      color: #f8f9fa;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }
    
    /* Navbar styling */
    .navbar {
      background: rgba(15, 23, 42, 0.95);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .navbar-brand {
      color: #f8f9fa !important;
      font-weight: 600;
    }

    /* Card styling */
    .app-card {
      border-radius: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background: rgba(15, 23, 42, 0.8);
      backdrop-filter: blur(12px);
    }
    
    .table {
        color: #f8f9fa;
    }
    .table thead th {
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        color: #0d6efd;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
    }
    .table tbody td {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding: 1rem;
        background: transparent !important;
        color: #f8f9fa !important;
    }
    .table tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }
    
    .badge-id {
        background: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        border: 1px solid rgba(13, 110, 253, 0.2);
        padding: 0.4rem 0.8rem;
        border-radius: 0.5rem;
        font-family: monospace;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand" href="dashboard-warden.php">
        <i class="bi bi-building-lock me-2"></i>Warden Dashboard
      </a>
      <div class="ms-auto text-secondary small">
        Prisoner Database Management
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container" style="margin-top: 100px; margin-bottom: 40px;">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2><i class="bi bi-people-fill me-2"></i>Registered Prisoners</h2>
          <a href="dashboard-warden.php" class="btn btn-outline-light btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Register New
          </a>
        </div>

        <div class="app-card p-4 p-md-5 shadow-lg">
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Prisoner ID</th>
                  <th>Full Name</th>
                  <th>Cell No</th>
                  <th>Crime Name</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><span class='badge-id'>" . $row["prisoner_id"] . "</span></td>";
                        echo "<td>" . $row["full_name"] . "</td>";
                        echo "<td>" . $row["cell_number"] . "</td>";
                        echo "<td>" . $row["crime"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center py-4 text-secondary'>No prisoners registered yet.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?php $conn->close(); ?>
