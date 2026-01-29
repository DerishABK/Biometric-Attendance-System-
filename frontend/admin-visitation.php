<?php
require_once '../backend/session_start.php';
if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'admin') {
Header("Location: index.php");
    exit();
}

// Generate randomized visitation data for demonstration
$visitor_names = ["James Wilson", "Mary Johnson", "Robert Brown", "Patricia Davis", "Michael Miller", "Linda Wilson", "William Moore", "Elizabeth Taylor", "David Anderson", "Barbara Thomas", "Richard Jackson", "Susan White", "Joseph Harris", "Jessica Martin", "Thomas Thompson", "Sarah Garcia"];
$relationships = ["Spouse", "Parent", "Sibling", "Legal Counsel", "Friend", "Child"];
$blocks = ["Block A - High Security", "Block B - General Tech", "Block C - Minimum Risk"];

$visitations = [];
for ($i = 0; $i < 15; $i++) {
    $visitations[] = [
        'id' => 1000 + $i,
        'visitor' => $visitor_names[array_rand($visitor_names)],
        'inmate' => $visitor_names[array_rand($visitor_names)], // Using same list for simplicity
        'relationship' => $relationships[array_rand($relationships)],
        'time_in' => date('H:i', strtotime("-" . rand(10, 180) . " minutes")),
        'block' => $blocks[array_rand($blocks)],
        'status' => (rand(0, 10) > 2) ? 'Active' : 'Completed'
    ];
}

// Group by block
$grouped_visits = [];
foreach ($visitations as $visit) {
    if ($visit['status'] === 'Active') {
        $grouped_visits[$visit['block']][] = $visit;
    }
}
ksort($grouped_visits);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Visitation Monitor | Prison PASS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="admin-style.css">
  <style>
    :root {
      --accent-color: #0d6efd;
    }
    /* Status Badge (Page Specific) */

    .stats-card {
      background: rgba(255,255,255,0.02);
      border: 1px solid var(--glass-border);
      padding: 1rem;
      border-radius: 10px;
    }

    .table {
        color: #f8fafc;
        margin-bottom: 0;
    }
    
    .table thead th {
        background: rgba(255, 255, 255, 0.03);
        border-bottom: 1px solid var(--glass-border);
        color: #cbd5e1;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 12px 15px;
    }

    .text-muted { color: #cbd5e1 !important; }
    
    .table tbody td {
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        padding: 10px 15px;
        vertical-align: middle;
        font-size: 0.85rem;
    }

    .block-divider {
        background: rgba(59, 130, 246, 0.1);
        color: var(--accent-color);
        font-weight: 700;
        font-size: 0.8rem;
        padding: 8px 15px;
        border-left: 4px solid var(--accent-color);
    }

    .badge-live {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.2);
        font-size: 0.7rem;
        padding: 4px 8px;
    }

    .pulse {
      animation: pulse-animation 2s infinite;
    }

    @keyframes pulse-animation {
      0% { opacity: 1; }
      50% { opacity: 0.5; }
      100% { opacity: 1; }
    }

    .badge-relationship {
        background: #e2e8f0;
        color: #0b0f19 !important;
        padding: 3px 10px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.75rem;
        display: inline-block;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    @media (max-width: 992px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.show { transform: translateX(0); }
      .main-content { margin-left: 0; padding: 1rem; }
      .sidebar-toggler { display: block !important; color: white !important; }
    }
  </style>
</head>
<body>

  <?php include 'admin-sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-link d-lg-none sidebar-toggler p-0">
          <i class="bi bi-list fs-2"></i>
        </button>
        <div>
          <h1 class="h4 font-weight-bold mb-1">Live Visitation Monitor</h1>
          <p class="text-muted small mb-0">Tracking active visits across all facility blocks</p>
        </div>
      </div>
      <div class="d-flex gap-2">
        <button onclick="exportVisitationPDF()" class="btn btn-outline-primary btn-sm border-opacity-25 px-3">
          <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
        </button>
        <div class="btn btn-dark btn-sm text-primary border-primary border-opacity-25 px-3">
            <i class="bi bi-record-fill pulse me-1 text-danger"></i> LIVE
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="stats-card">
          <div class="text-muted small mb-1">Active Visits</div>
          <div class="h3 mb-0 font-weight-bold"><?php echo rand(12, 18); ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stats-card">
          <div class="text-muted small mb-1">Scheduled Today</div>
          <div class="h3 mb-0 font-weight-bold"><?php echo rand(40, 55); ?></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stats-card">
          <div class="text-muted small mb-1">Banned Visitors</div>
          <div class="h3 mb-0 font-weight-bold text-danger"><?php echo rand(5, 10); ?></div>
        </div>
      </div>
    </div>

    <div class="glass-card p-0 overflow-hidden">
      <table class="table visitation-table">
        <thead>
          <tr>
            <th>Visitor Name</th>
            <th>Inmate</th>
            <th>Relationship</th>
            <th>Time In</th>
            <th class="text-center">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($grouped_visits as $block => $visits): ?>
            <tr>
              <td colspan="5" class="block-divider">
                <i class="bi bi-geo-alt-fill me-2"></i> <?php echo $block; ?>
              </td>
            </tr>
            <?php foreach ($visits as $visit): ?>
              <tr>
                <td><strong><?php echo $visit['visitor']; ?></strong></td>
                <td><?php echo $visit['inmate']; ?></td>
                <td><span class="badge-relationship"><?php echo $visit['relationship']; ?></span></td>
                <td><i class="bi bi-clock me-1 text-primary small"></i> <?php echo $visit['time_in']; ?></td>
                <td class="text-center">
                  <span class="badge badge-live">
                    <span class="status-indicator status-online me-1"></span> Active
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
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
    function exportVisitationPDF() {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF('p', 'mm', 'a4');
      
      doc.setFillColor(15, 23, 42);
      doc.rect(0, 0, 210, 20, 'F');
      doc.setTextColor(255, 255, 255);
      doc.setFontSize(16);
      doc.text("DAILY VISITATION LOG", 14, 13);
      doc.setFontSize(10);
      doc.text("Date: <?php echo date('d M Y'); ?>", 170, 13);
      
      const table = document.querySelector(".visitation-table");
      doc.autoTable({
        html: table,
        startY: 25,
        theme: 'striped',
        headStyles: { fillColor: [15, 23, 42], textColor: [255, 255, 255], fontSize: 10 },
        bodyStyles: { fontSize: 9 },
        didDrawCell: (data) => {
            if (data.row.raw.classList && data.row.raw.classList.contains('block-divider')) {
                // Custom styling for divider rows in PDF if needed
            }
        },
        didDrawPage: function(data) {
          doc.setFontSize(8);
          doc.setTextColor(150);
          doc.text("Prison Management Security System - Confidential", 14, doc.internal.pageSize.height - 10);
        }
      });
      
      doc.save("Visitation_Log_<?php echo date('d_M_Y'); ?>.pdf");
    }
  </script>

</body>
</html>
