<?php
require_once '../backend/session_start.php';
if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit();
}
include '../backend/db_connect.php';

// Fetch staff data ordered by hierarchy
$sql = "SELECT user_id, full_name, role, designation, status, assigned_wing, shift_type, contact_ext, joining_date FROM users 
        ORDER BY FIELD(designation, 'Superintendent of Police', 'Sub Inspector', 'Assistant Sub Inspector', 'Constable'), full_name";
$result = $conn->query($sql);

$staff_list = [];
$stats = ['total' => 0, 'on_duty' => 0, 'off_duty' => 0, 'on_leave' => 0];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $staff_list[] = $row;
        $stats['total']++;
        if($row['status'] == 'On Duty') $stats['on_duty']++;
        elseif($row['status'] == 'Off Duty') $stats['off_duty']++;
        else $stats['on_leave']++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Staff Directory - Prison Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="admin-style.css">
  <style>
    /* Compact Table (Page Specific) */
    .staff-table { width: 100%; border-collapse: collapse; }
    .staff-table th {
      text-align: left; color: rgba(255, 255, 255, 0.4); font-size: 0.65rem;
      text-transform: uppercase; letter-spacing: 0.5px; padding: 0.5rem 0.75rem;
      border-bottom: 1px solid var(--glass-border);
    }
    .staff-table td { padding: 0.5rem 0.75rem; border-bottom: 1px solid rgba(255, 255, 255, 0.03); vertical-align: middle; }
    .staff-table tr:hover { background: rgba(255, 255, 255, 0.02); }
    
    .group-header { background: rgba(13, 110, 253, 0.05); color: var(--accent-color); font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; }
    .group-header td { padding: 0.35rem 0.75rem; border-left: 3px solid var(--accent-color); }

    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
    .status-on { background: #10b981; box-shadow: 0 0 10px #10b981; animation: pulse 2s infinite; }
    .status-off { background: #64748b; }
    .status-leave { background: #f59e0b; }
    
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.4; } 100% { opacity: 1; } }

    .badge-rank {
      padding: 0.15rem 0.45rem; border-radius: 4px; font-size: 0.65rem; font-weight: 700;
      background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .rank-sp { color: #ffc107; border-color: rgba(255, 193, 7, 0.2); }
    .rank-si { color: #10b981; border-color: rgba(16, 185, 129, 0.2); }
    .rank-asi { color: #0ea5e9; border-color: rgba(14, 165, 233, 0.2); }

    .summary-bar { display: flex; gap: 1.5rem; margin-bottom: 1rem; padding: 0.5rem 1rem; background: rgba(255,255,255,0.03); border-radius: 0.5rem; border: 1px solid var(--glass-border); }
    .summary-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; font-weight: 600; color: rgba(255,255,255,0.6); }
    .summary-item b { color: #fff; font-size: 0.9rem; }
  </style>
</head>
<body>
  <?php include 'admin-sidebar.php'; ?>

  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h5 font-weight-bold mb-0">Personnel Management</h1>
      <div class="d-flex gap-2 align-items-center">
        <button onclick="exportStaffPDF()" class="btn btn-outline-primary btn-sm border-opacity-25" style="font-size: 0.75rem;">
          <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
        </button>
        <div class="small text-muted">Last Update: <?php echo date('h:i A'); ?></div>
      </div>
    </div>

    <div class="summary-bar">
      <div class="summary-item">Total Force: <b><?php echo $stats['total']; ?></b></div>
      <div class="summary-item"><span class="status-dot status-on"></span> On Duty: <b><?php echo $stats['on_duty']; ?></b></div>
      <div class="summary-item"><span class="status-dot status-off"></span> Off Duty: <b><?php echo $stats['off_duty']; ?></b></div>
      <div class="summary-item"><span class="status-dot status-leave"></span> Leave: <b><?php echo $stats['on_leave']; ?></b></div>
    </div>

    <div class="app-card p-0">
      <table class="staff-table">
        <thead>
          <tr>
            <th>Official ID</th>
            <th>Name</th>
            <th>Rank/Designation</th>
            <th>Assigned Wing</th>
            <th>Shift</th>
            <th>Ext.</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $current_designation = "";
          foreach($staff_list as $staff): 
            if($staff['designation'] !== $current_designation):
              $current_designation = $staff['designation'];
          ?>
            <tr class="group-header">
              <td colspan="7"><?php echo $current_designation; ?></td>
            </tr>
          <?php endif; ?>
          <tr>
            <td><span style="color: rgba(255,255,255,0.4); font-family: monospace;">#<?php echo strtoupper($staff['user_id']); ?></span></td>
            <td class="fw-bold"><?php echo $staff['full_name']; ?></td>
            <td>
              <?php 
                $badge_class = "";
                if($staff['designation'] == 'Superintendent of Police') $badge_class = "rank-sp";
                elseif($staff['designation'] == 'Sub Inspector') $badge_class = "rank-si";
                elseif($staff['designation'] == 'Assistant Sub Inspector') $badge_class = "rank-asi";
              ?>
              <span class="badge-rank <?php echo $badge_class; ?>"><?php echo $staff['designation']; ?></span>
            </td>
            <td class="text-info opacity-75 small"><?php echo $staff['assigned_wing']; ?></td>
            <td class="opacity-75 small"><?php echo $staff['shift_type']; ?></td>
            <td class="font-monospace small">x<?php echo $staff['contact_ext']; ?></td>
            <td>
              <?php if($staff['status'] == 'On Duty'): ?>
                <span class="text-success small fw-bold"><span class="status-dot status-on"></span>Active</span>
              <?php elseif($staff['status'] == 'Off Duty'): ?>
                <span class="text-secondary small fw-bold"><span class="status-dot status-off"></span>Offline</span>
              <?php else: ?>
                <span class="text-warning small fw-bold"><span class="status-dot status-leave"></span>Leave</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
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
    function exportStaffPDF() {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF('l', 'mm', 'a4'); // Landscape
      
      // Header
      doc.setFillColor(13, 110, 253);
      doc.rect(0, 0, 297, 20, 'F');
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(18);
      doc.text("OFFICIAL STAFF DIRECTORY", 14, 13);
      doc.setFontSize(10);
      doc.text("Generated on: <?php echo date('d M Y, h:i A'); ?>", 230, 13);
      
      // Table
      const table = document.querySelector(".staff-table");
      doc.autoTable({
        html: table,
        startY: 25,
        theme: 'striped',
        headStyles: { fillColor: [15, 23, 42], textColor: [255, 255, 255], fontSize: 10, halign: 'center' },
        bodyStyles: { fontSize: 9, halign: 'left' },
        columnStyles: {
            0: { halign: 'center', fontStyle: 'bold' },
            6: { halign: 'center' }
        },
        didDrawPage: function(data) {
          // Footer
          doc.setFontSize(8);
          doc.setTextColor(150);
          doc.text("Page " + doc.internal.getNumberOfPages(), 14, doc.internal.pageSize.height - 10);
          doc.text("Prison Administration Security System - Confidential Report", 210, doc.internal.pageSize.height - 10);
        }
      });
      
      const filename = "Staff_Report_<?php echo date('d_M_Y'); ?>.pdf";
      doc.save(filename);
    }
  </script>
</body>
</html>
