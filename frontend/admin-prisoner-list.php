<?php
require_once '../backend/session_start.php';
if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit();
}
include '../backend/db_connect.php';

// Fetch prisoner data ordered by Block/Wing
$sql = "SELECT prisoner_id, full_name, block_wing, cell_number, crime, admission_date, expected_release FROM prisoners 
        ORDER BY block_wing, full_name";
$result = $conn->query($sql);

$prisoner_list = [];
$stats = ['total' => 0, 'high_security' => 0, 'general' => 0];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $prisoner_list[] = $row;
        $stats['total']++;
        if (strpos(strtolower($row['block_wing']), 'high') !== false || strpos(strtolower($row['block_wing']), 'cell block a') !== false) {
            $stats['high_security']++;
        } else {
            $stats['general']++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inmate Directory - Prison Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    :root {
      --sidebar-width: 220px;
      --glass-bg: rgba(15, 23, 42, 0.85);
      --glass-border: rgba(255, 255, 255, 0.08);
      --accent-color: #0d6efd;
    }
    body {
      min-height: 100vh;
      background: radial-gradient(circle at top left, #1e3a8a, #050505 60%);
      color: #fff !important;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      margin: 0;
      overflow-x: hidden;
      font-size: 0.85rem;
    }
    * { color: inherit; }
    
    /* Sidebar (Synchronized) */
    .sidebar {
      position: fixed; top: 0; left: 0; bottom: 0; width: var(--sidebar-width);
      background: var(--glass-bg); backdrop-filter: blur(15px); border-right: 1px solid var(--glass-border);
      color: #f8f9fa; padding: 1rem 0; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); z-index: 1000;
    }
    .sidebar-header { padding: 0 1.25rem 1.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); margin-bottom: 0.75rem; }
    .sidebar-brand { color: #fff; text-decoration: none; font-size: 1.2rem; font-weight: 700; display: flex; align-items: center; gap: 0.6rem; }
    .sidebar-brand i { color: var(--accent-color); }
    .nav-link { color: rgba(255, 255, 255, 0.6) !important; padding: 0.65rem 1.25rem; margin: 0.15rem 0.5rem; border-radius: 0.65rem; display: flex; align-items: center; gap: 0.75rem; transition: all 0.2s ease; font-weight: 500; font-size: 0.85rem; }
    .nav-link:hover, .nav-link.active { background: rgba(13, 110, 253, 0.1); color: #fff !important; }
    .nav-link i { font-size: 1.1rem; }

    .main-content { margin-left: var(--sidebar-width); padding: 1.25rem; }
    
    /* Compact Layout */
    .summary-bar { display: flex; gap: 1.5rem; margin-bottom: 1.25rem; padding: 0.6rem 1rem; background: rgba(255,255,255,0.03); border-radius: 0.75rem; border: 1px solid var(--glass-border); }
    .summary-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; font-weight: 600; color: rgba(255,255,255,0.6); }
    .summary-item b { color: #fff; font-size: 0.95rem; }

    .app-card {
      background: var(--glass-bg); backdrop-filter: blur(10px); border-radius: 0.85rem;
      border: 1px solid var(--glass-border); padding: 0.75rem; box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    /* Table Styling */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th {
      text-align: left; color: rgba(255, 255, 255, 0.4); font-size: 0.65rem;
      text-transform: uppercase; letter-spacing: 0.5px; padding: 0.5rem 0.75rem;
      border-bottom: 1px solid var(--glass-border);
    }
    .data-table td { padding: 0.55rem 0.75rem; border-bottom: 1px solid rgba(255, 255, 255, 0.03); vertical-align: middle; }
    .data-table tr:hover { background: rgba(255, 255, 255, 0.02); }
    
    .group-header { background: rgba(13, 110, 253, 0.06); color: var(--accent-color); font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; }
    .group-header td { padding: 0.4rem 0.75rem; border-left: 3px solid var(--accent-color); }

    .inmate-id { color: rgba(255,255,255,0.4); font-family: monospace; font-size: 0.8rem; }
    .inmate-name { font-weight: 600; color: #fff; }
    .crime-tag { color: #f87171; font-size: 0.8rem; font-weight: 500; }
    .release-date { font-size: 0.8rem; color: #fbbf24; font-weight: 600; }
    
    .badge-wing {
      padding: 0.15rem 0.45rem; border-radius: 4px; font-size: 0.65rem; font-weight: 700;
      background: rgba(13, 110, 253, 0.1); border: 1px solid rgba(13, 110, 253, 0.2); color: var(--accent-color);
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-header">
      <a href="dashboard-admin.php" class="sidebar-brand">
        <i class="bi bi-shield-lock-fill"></i>
        <span>Prison Admin</span>
      </a>
    </div>
    
    <div class="sidebar-menu">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a href="dashboard-admin.php" class="nav-link">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="admin-prisoner-list.php" class="nav-link active">
            <i class="bi bi-people-fill"></i>
            <span>Prisoners</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="staff-list.php" class="nav-link">
            <i class="bi bi-person-badge-fill"></i>
            <span>Staff</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="admin-visitation.php" class="nav-link">
            <i class="bi bi-calendar-event-fill"></i>
            <span>Visitation</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="admin-settings.php" class="nav-link">
            <i class="bi bi-gear-fill"></i>
            <span>Settings</span>
          </a>
        </li>
        <li class="nav-item mt-auto">
          <a href="../backend/logout.php" class="nav-link fw-bold" style="color: #ff4d4d !important;">
            <i class="bi bi-box-arrow-right me-2"></i>
            <span>Logout</span>
          </a>
        </li>
      </ul>
    </div>
  </div>

  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h1 class="h5 font-weight-bold mb-0">Inmate Directory</h1>
        <p class="text-muted small mb-0">Full record of incarcerated personnel</p>
      </div>
      <div class="d-flex gap-2 align-items-center">
        <button onclick="exportInmatePDF()" class="btn btn-outline-primary btn-sm border-opacity-25" style="font-size: 0.75rem;">
          <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
        </button>
        <div class="bg-primary bg-opacity-10 border border-primary border-opacity-20 rounded px-3 py-1 text-primary fw-bold small">Admin View</div>
      </div>
    </div>

    <div class="summary-bar">
      <div class="summary-item">Total Population: <b><?php echo $stats['total']; ?></b></div>
      <div class="summary-item"><i class="bi bi-shield-exclamation text-danger"></i> High Security: <b><?php echo $stats['high_security']; ?></b></div>
      <div class="summary-item"><i class="bi bi-shield-check text-success"></i> General Pop: <b><?php echo $stats['general']; ?></b></div>
    </div>

    <div class="app-card p-0">
      <table class="data-table">
        <thead>
          <tr>
            <th>Official ID</th>
            <th>Full Name</th>
            <th>Location</th>
            <th>Committed Crime</th>
            <th>Admission</th>
            <th>Exp. Release</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $current_wing = "";
          if (count($prisoner_list) > 0):
            foreach($prisoner_list as $inmate): 
              if($inmate['block_wing'] !== $current_wing):
                $current_wing = $inmate['block_wing'];
          ?>
            <tr class="group-header">
              <td colspan="6"><i class="bi bi-geo-alt-fill me-2"></i><?php echo $current_wing; ?></td>
            </tr>
          <?php endif; ?>
          <tr>
            <td><span class="inmate-id">#<?php echo strtoupper($inmate['prisoner_id']); ?></span></td>
            <td><span class="inmate-name"><?php echo $inmate['full_name']; ?></span></td>
            <td>
              <span class="badge-wing">Cell <?php echo $inmate['cell_number']; ?></span>
            </td>
            <td class="crime-tag"><?php echo $inmate['crime']; ?></td>
            <td class="opacity-75 small"><?php echo date('M d, Y', strtotime($inmate['admission_date'])); ?></td>
            <td class="release-date"><?php echo date('M d, Y', strtotime($inmate['expected_release'])); ?></td>
          </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="6" class="text-center py-5 text-muted">No inmates recorded in the central database.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
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
      const doc = new jsPDF('l', 'mm', 'a4'); // Landscape for more space
      
      // Theme Header
      doc.setFillColor(15, 23, 42); // Dark Admin Theme
      doc.rect(0, 0, 297, 22, 'F');
      
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(20);
      doc.text("OFFICIAL PRISONER ROSTER", 14, 14);
      
      doc.setFontSize(9);
      doc.text("SECURITY CLASSIFICATION: CONFIDENTIAL", 230, 8);
      doc.text("GENERATED: <?php echo date('d M Y, h:i A'); ?>", 230, 15);
      
      // Table Data
      const table = document.querySelector(".data-table");
      doc.autoTable({
        html: table,
        startY: 28,
        theme: 'striped',
        headStyles: { 
            fillColor: [13, 110, 253], 
            textColor: [255, 255, 255], 
            fontSize: 10, 
            halign: 'center',
            cellPadding: 4
        },
        bodyStyles: { fontSize: 9, halign: 'left', cellPadding: 3 },
        columnStyles: {
            0: { halign: 'center', fontStyle: 'bold' },
            2: { halign: 'center' },
            5: { halign: 'center', textColor: [184, 134, 11] } // Golden for release date
        },
        didDrawPage: function(data) {
          // Footer
          doc.setFontSize(8);
          doc.setTextColor(100);
          doc.text("Prison Management & Attendance Monitoring System", 14, doc.internal.pageSize.height - 10);
          doc.text("Page " + doc.internal.getNumberOfPages(), 270, doc.internal.pageSize.height - 10);
        }
      });
      
      doc.save("Prisoner_Roster_<?php echo date('d_M_Y'); ?>.pdf");
    }
  </script>
</body>
</html>
