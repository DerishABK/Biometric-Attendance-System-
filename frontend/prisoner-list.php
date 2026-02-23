<?php
require_once '../backend/session_start.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
include '../backend/db_connect.php';

// Fetch prisoners from database grouped by block/wing and ordered by most recent first
$sql = "SELECT * FROM prisoners ORDER BY block_wing ASC, created_at DESC";
$result = $conn->query($sql);

$prisoners_by_section = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $section = $row['block_wing'] ?: 'Unassigned Section';
        $prisoners_by_section[$section][] = $row;
    }
}
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

    .clickable-row {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .clickable-row:hover {
        background: rgba(13, 110, 253, 0.1) !important;
    }

    /* Modal Styling */
    .modal-content {
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #f8f9fa;
    }
    .modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .modal-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    .detail-label {
        color: #0d6efd;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    .detail-value {
        font-size: 1rem;
        margin-bottom: 1rem;
    }
    .detail-img {
        width: 150px;
        height: 180px;
        object-fit: cover;
        border-radius: 0.5rem;
        border: 2px solid rgba(13, 110, 253, 0.3);
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
                if (!empty($prisoners_by_section)) {
                    foreach ($prisoners_by_section as $section => $prisoners) {
                        // Section Header Row
                        echo "<tr class='table-dark'><td colspan='5' class='py-3 ps-3 fw-bold text-info' style='background: rgba(13, 110, 253, 0.15); font-size: 1.1rem;'><i class='bi bi-grid-3x3-gap-fill me-2'></i>" . htmlspecialchars($section) . "</td></tr>";
                        
                        foreach ($prisoners as $row) {
                            $photo = !empty($row['photo_path']) ? $row['photo_path'] : 'https://ui-avatars.com/api/?name=' . urlencode($row['full_name']) . '&background=random';
                            // Prepare data for JS modal
                            $prisoner_data = json_encode($row);
                            echo "<tr class='clickable-row' data-prisoner='" . htmlspecialchars($prisoner_data, ENT_QUOTES, 'UTF-8') . "' onclick='showPrisonerDetails(this)'>";
                            echo "<td><img src='" . $photo . "' class='rounded' style='width:35px; height:35px; object-fit:cover;'></td>";
                            echo "<td><span class='badge-id'>" . $row["prisoner_id"] . "</span></td>";
                            echo "<td>" . $row["full_name"] . "</td>";
                            echo "<td>" . $row["cell_number"] . "</td>";
                            echo "<td>" . $row["crime"] . "</td>";
                            echo "</tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center py-4 text-secondary'>No prisoners registered yet.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Prisoner Details Modal -->
  <div class="modal fade" id="prisonerDetailsModal" tabindex="-1" aria-labelledby="prisonerDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="prisonerDetailsModalLabel"><i class="bi bi-person-badge me-2"></i>Prisoner Details</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row">
            <div class="col-md-4 text-center mb-4 mb-md-0">
              <img id="modal-photo" src="" alt="Prisoner Photo" class="detail-img shadow">
              <div class="mt-3">
                <span id="modal-id" class="badge-id"></span>
              </div>
            </div>
            <div class="col-md-8">
              <div class="row">
                <div class="col-md-6">
                  <div class="detail-label">Full Name</div>
                  <div id="modal-name" class="detail-value fw-bold"></div>
                  
                  <div class="detail-label">Date of Birth</div>
                  <div id="modal-dob" class="detail-value"></div>
                  
                  <div class="detail-label">Gender / Nationality</div>
                  <div class="detail-value"><span id="modal-gender"></span> / <span id="modal-nationality"></span></div>
                </div>
                <div class="col-md-6">
                  <div class="detail-label">Block / Cell</div>
                  <div class="detail-value"><span id="modal-block"></span> / <span id="modal-cell"></span></div>
                  
                  <div class="detail-label">Crime / Offense</div>
                  <div id="modal-crime" class="detail-value"></div>
                  
                  <div class="detail-label">Emergency Contact</div>
                  <div id="modal-contact" class="detail-value"></div>
                </div>
                <div class="col-12 border-top border-secondary pt-3 mt-2">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="detail-label">Sentence</div>
                      <div id="modal-sentence" class="detail-value"></div>
                    </div>
                    <div class="col-md-4">
                      <div class="detail-label">Admission Date</div>
                      <div id="modal-admission" class="detail-value"></div>
                    </div>
                    <div class="col-md-4">
                      <div class="detail-label">Expected Release</div>
                      <div id="modal-release" class="detail-value"></div>
                    </div>
                  </div>
                  <div class="detail-label">Address</div>
                  <div id="modal-address" class="detail-value small"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
          <button onclick="exportSingleInmatePDF()" class="btn btn-primary">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download Details
          </button>
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
    const prisonerModal = new bootstrap.Modal(document.getElementById('prisonerDetailsModal'));
    let selectedPrisoner = null;

    function showPrisonerDetails(row) {
        const data = JSON.parse(row.getAttribute('data-prisoner'));
        selectedPrisoner = data;
        
        document.getElementById('modal-photo').src = data.photo_path || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(data.full_name) + '&background=random';
        document.getElementById('modal-id').innerText = data.prisoner_id;
        document.getElementById('modal-name').innerText = data.full_name;
        document.getElementById('modal-dob').innerText = data.dob || 'N/A';
        document.getElementById('modal-gender').innerText = data.gender || 'N/A';
        document.getElementById('modal-nationality').innerText = data.nationality || 'N/A';
        document.getElementById('modal-block').innerText = data.block_wing || 'N/A';
        document.getElementById('modal-cell').innerText = data.cell_number || 'N/A';
        document.getElementById('modal-crime').innerText = data.crime || 'N/A';
        document.getElementById('modal-contact').innerText = data.contact_number || 'N/A';
        document.getElementById('modal-sentence').innerText = data.sentence_duration || 'N/A';
        document.getElementById('modal-admission').innerText = data.admission_date || 'N/A';
        document.getElementById('modal-release').innerText = data.expected_release || 'N/A';
        document.getElementById('modal-address').innerText = data.address || 'N/A';

        prisonerModal.show();
    }

    function exportInmatePDF() {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF('p', 'mm', 'a4');
      
      doc.setFillColor(15, 23, 42);
      doc.rect(0, 0, 210, 22, 'F');
      
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(18);
      doc.text("REGISTERED PRISONER LIST", 14, 14);
      
      doc.setFontSize(8);
      doc.text("GENERATED: <?php echo date('d M Y, h:i A'); ?>", 150, 15);
      
      const table = document.querySelector(".table");
      // Create a clone to remove clickable rows and JS data for PDF export
      const tableClone = table.cloneNode(true);
      tableClone.querySelectorAll('.clickable-row').forEach(tr => {
          tr.removeAttribute('onclick');
          tr.removeAttribute('data-prisoner');
      });

      doc.autoTable({
        html: tableClone,
        startY: 28,
        theme: 'striped',
        headStyles: { fillColor: [13, 110, 253], textColor: [255, 255, 255], fontSize: 10 },
        bodyStyles: { fontSize: 9, cellPadding: 2, minCellHeight: 22 },
        columnStyles: { 0: { cellWidth: 25, halign: 'center' } },
        didDrawCell: function(data) {
          if (data.section === 'body' && data.column.index === 0) {
            const img = data.cell.raw.querySelector('img');
            if (img && img.src) {
              doc.addImage(img.src, 'JPEG', data.cell.x + 4, data.cell.y + 2, 17, 18);
            }
          }
        }
      });
      
      doc.save("Prisoner_List_<?php echo date('d_M_Y'); ?>.pdf");
    }

    function exportSingleInmatePDF() {
        if (!selectedPrisoner) return;
        
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');
        
        // Header
        doc.setFillColor(15, 23, 42);
        doc.rect(0, 0, 210, 30, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(22);
        doc.text("PRISONER RECORD", 14, 20);
        doc.setFontSize(10);
        doc.text("Generated on: " + new Date().toLocaleString(), 140, 20);

        // Photo
        const photo = selectedPrisoner.photo_path || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(selectedPrisoner.full_name) + '&background=random';
        doc.addImage(photo, 'JPEG', 150, 40, 40, 50);
        doc.rect(150, 40, 40, 50);

        // Details
        doc.setTextColor(13, 110, 253);
        doc.setFontSize(12);
        doc.text("PERSONAL INFORMATION", 14, 45);
        doc.setLineWidth(0.5);
        doc.line(14, 47, 80, 47);

        doc.setTextColor(0, 0, 0);
        doc.setFontSize(10);
        let y = 55;
        const details = [
            ["ID:", selectedPrisoner.prisoner_id],
            ["Full Name:", selectedPrisoner.full_name],
            ["Date of Birth:", selectedPrisoner.dob || 'N/A'],
            ["Gender:", selectedPrisoner.gender || 'N/A'],
            ["Nationality:", selectedPrisoner.nationality || 'N/A'],
            ["Emergency Contact:", selectedPrisoner.contact_number || 'N/A'],
            ["Address:", selectedPrisoner.address || 'N/A']
        ];

        details.forEach(item => {
            doc.setFont(undefined, 'bold');
            doc.text(item[0], 14, y);
            doc.setFont(undefined, 'normal');
            if (item[0] === "Address:") {
                const splitText = doc.splitTextToSize(item[1], 120);
                doc.text(splitText, 50, y);
                y += (splitText.length * 5) + 2;
            } else {
                doc.text(item[1], 50, y);
                y += 7;
            }
        });

        y += 5;
        doc.setTextColor(13, 110, 253);
        doc.text("INCARCERATION DETAILS", 14, y);
        doc.line(14, y + 2, 80, y + 2);
        y += 10;
        doc.setTextColor(0, 0, 0);

        const incarceration = [
            ["Block/Wing:", selectedPrisoner.block_wing || 'N/A'],
            ["Cell Number:", selectedPrisoner.cell_number || 'N/A'],
            ["Crime/Offense:", selectedPrisoner.crime || 'N/A'],
            ["Sentence:", selectedPrisoner.sentence_duration || 'N/A'],
            ["Admission Date:", selectedPrisoner.admission_date || 'N/A'],
            ["Expected Release:", selectedPrisoner.expected_release || 'N/A']
        ];

        incarceration.forEach(item => {
            doc.setFont(undefined, 'bold');
            doc.text(item[0], 14, y);
            doc.setFont(undefined, 'normal');
            doc.text(item[1], 50, y);
            y += 7;
        });

        doc.save("Prisoner_Record_" + selectedPrisoner.prisoner_id + ".pdf");
    }
  </script>
</body>
</html>
<?php $conn->close(); ?>
