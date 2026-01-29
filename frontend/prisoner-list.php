<?php
require_once '../backend/session_start.php';
include '../backend/db_connect.php';

// Fetch prisoners from database
$sql = "SELECT prisoner_id, full_name, cell_number, crime, photo_path FROM prisoners ORDER BY created_at DESC";
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
      <a class="navbar-brand" href="dashboard-<?php echo $_SESSION['role']; ?>.php">
        <i class="bi bi-building-lock me-2"></i>Prison Monitor
      </a>
      <div class="ms-auto d-flex align-items-center">
        <div class="dropdown">
          <a href="#" class="nav-link dropdown-toggle d-flex align-items-center text-white text-decoration-none" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; color: white;">
              <i class="bi bi-person-fill"></i>
            </div>
            <span>Profile</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow-lg" aria-labelledby="profileDropdown">
            <li><a class="dropdown-item" href="dashboard-<?php echo $_SESSION['role']; ?>.php"><i class="bi bi-speedometer2 me-2"></i> Main Page</a></li>
            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-circle me-2"></i> Profile</a></li>
            <li><hr class="dropdown-divider border-secondary"></li>
            <li><a class="dropdown-item text-danger fw-bold" href="../backend/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container" style="margin-top: 100px; margin-bottom: 40px;">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2><i class="bi bi-people-fill me-2"></i>Registered Prisoners</h2>
          <div class="d-flex gap-2">
            <button onclick="exportInmatePDF()" class="btn btn-primary btn-sm">
              <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
            </button>
            <a href="dashboard-warden.php" class="btn btn-outline-light btn-sm">
              <i class="bi bi-plus-lg me-1"></i> Register New
            </a>
          </div>
        </div>

        <div class="app-card p-4 p-md-5 shadow-lg">
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Photo</th>
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
                        $photo = !empty($row['photo_path']) ? $row['photo_path'] : 'https://ui-avatars.com/api/?name=' . urlencode($row['full_name']) . '&background=random';
                        echo "<tr>";
                        echo "<td><img src='" . $photo . "' class='rounded' style='width:35px; height:35px; object-fit:cover;'></td>";
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

  <!-- jsPDF & AutoTable -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.6.0/jspdf.plugin.autotable.min.js"></script>

  <script>
    function exportInmatePDF() {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF('p', 'mm', 'a4'); // Portrait
      
      doc.setFillColor(15, 23, 42);
      doc.rect(0, 0, 210, 22, 'F');
      
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(18);
      doc.text("REGISTERED PRISONER LIST", 14, 14);
      
      doc.setFontSize(8);
      doc.text("GENERATED: <?php echo date('d M Y, h:i A'); ?>", 150, 15);
      
      const table = document.querySelector(".table");
      doc.autoTable({
        html: table,
        startY: 28,
        theme: 'striped',
        headStyles: { 
            fillColor: [13, 110, 253], 
            textColor: [255, 255, 255], 
            fontSize: 10
        },
        bodyStyles: { fontSize: 9, cellPadding: 2, minCellHeight: 22 }, // Increase row height for photos
        columnStyles: {
            0: { cellWidth: 25, halign: 'center' } // Wider column for photo
        },
        didDrawCell: function(data) {
          if (data.section === 'body' && data.column.index === 0) {
            const img = data.cell.raw.querySelector('img');
            if (img && img.src) {
              // Properly size and center the image within the cell to avoid cropping
              doc.addImage(img.src, 'JPEG', data.cell.x + 4, data.cell.y + 2, 17, 18);
            }
          }
        }
      });
      
      doc.save("Prisoner_List_<?php echo date('d_M_Y'); ?>.pdf");
    }
  </script>
</body>
</html>
<?php $conn->close(); ?>
