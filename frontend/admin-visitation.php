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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    :root {
      --primary-blue: #0d6efd;
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
    }

    * { color: inherit; }

    /* Sidebar Styling */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      width: var(--sidebar-width);
      background: var(--glass-bg);
      backdrop-filter: blur(15px);
      border-right: 1px solid var(--glass-border);
      color: #f8f9fa;
      padding: 1rem 0;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 1000;
    }

    .sidebar-header {
      padding: 0 1.25rem 1.5rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      margin-bottom: 0.75rem;
    }

    .sidebar-brand {
      color: #fff;
      text-decoration: none;
      font-size: 1.2rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    .sidebar-brand i {
      color: var(--accent-color);
    }

    .nav-link {
      color: rgba(255, 255, 255, 0.6) !important;
      padding: 0.65rem 1.25rem;
      margin: 0.15rem 0.5rem;
      border-radius: 0.65rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      transition: all 0.2s ease;
      font-weight: 500;
      font-size: 0.85rem;
    }

    .nav-link:hover, .nav-link.active {
      background: rgba(13, 110, 253, 0.1);
      color: #fff !important;
    }

    .nav-link i {
      font-size: 1.1rem;
    }

    /* Main Content */
    .main-content {
      margin-left: var(--sidebar-width);
      padding: 1.5rem;
      min-height: 100vh;
    }

    .glass-card {
      background: var(--glass-bg);
      backdrop-filter: blur(12px);
      border: 1px solid var(--glass-border);
      border-radius: 12px;
      padding: 1.25rem;
      margin-bottom: 20px;
    }

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
          <a href="admin-prisoner-list.php" class="nav-link">
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
          <a href="admin-visitation.php" class="nav-link active">
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
        <li class="nav-item mt-4">
          <a href="../backend/logout.php" class="nav-link fw-bold" style="color: #ff4d4d !important;">
            <i class="bi bi-box-arrow-right me-2"></i>
            <span>Logout</span>
          </a>
        </li>
      </ul>
    </div>
  </div>

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
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarToggler = document.querySelector('.sidebar-toggler');
      const sidebar = document.querySelector('.sidebar');
      if (sidebarToggler) {
        sidebarToggler.addEventListener('click', function() {
          sidebar.classList.toggle('show');
        });
      }
      document.addEventListener('click', function(event) {
        if (window.innerWidth < 992 && sidebar.classList.contains('show')) {
          if (!sidebar.contains(event.target) && !sidebarToggler.contains(event.target)) {
            sidebar.classList.remove('show');
          }
        }
      });
    });
  </script>
</body>
</html>
