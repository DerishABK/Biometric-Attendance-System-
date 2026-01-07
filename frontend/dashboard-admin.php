<?php
require_once '../backend/session_start.php';
if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard - Prisoner Attendance System</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  
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
    
    .text-muted { color: rgba(255, 255, 255, 0.5) !important; }
    .text-primary { color: var(--accent-color) !important; }
    .text-success { color: #10b981 !important; }
    .text-info { color: #0ea5e9 !important; }
    .text-warning { color: #f59e0b !important; }
    .text-danger { color: #ef4444 !important; }

    /* Button Hover Fixes */
    .btn-outline-light:hover {
      background-color: #fff !important;
      color: #000 !important;
    }
    .btn-outline-light:hover * {
      color: #000 !important;
    }
    
    /* Sidebar */
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
      font-size: 1rem;
      width: 18px;
    }
    
    /* Main Content */
    .main-content {
      margin-left: var(--sidebar-width);
      padding: 1.5rem;
      min-height: 100vh;
    }
    
    .top-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.25rem;
    }
    
    .page-title h1 {
      font-size: 1.4rem;
      font-weight: 700;
      margin: 0;
    }
    
    .user-avatar {
      width: auto;
      min-width: 60px;
      height: 32px;
      padding: 0 12px;
      border-radius: 8px;
      background: rgba(13, 110, 253, 0.1);
      border: 1px solid rgba(13, 110, 253, 0.2);
      color: var(--accent-color);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      cursor: pointer;
      font-size: 0.8rem;
    }
    
    /* App Cards */
    .app-card {
      background: var(--glass-bg);
      backdrop-filter: blur(12px);
      border-radius: 1rem;
      border: 1px solid var(--glass-border);
      padding: 1rem;
      margin-bottom: 1rem;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      color: #fff !important;
      transition: all 0.3s ease;
    }
    
    .app-card:hover {
      transform: translateY(-5px);
      border-color: rgba(13, 110, 253, 0.4);
      box-shadow: 0 8px 30px rgba(0,0,0,0.4);
    }
    
    .card-title {
      font-size: 0.95rem;
      font-weight: 600;
      color: #fff;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }
    
    /* Stat Metrics */
    .stat-metric {
      display: flex;
      align-items: center;
      gap: 1.25rem;
      position: relative;
      overflow: hidden;
    }
    
    .stat-icon {
      font-size: 2.25rem;
      opacity: 0.6;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      color: var(--accent-color);
    }
    
    .app-card:hover .stat-icon {
      opacity: 1;
      transform: scale(1.3) rotate(10deg);
      filter: drop-shadow(0 0 10px rgba(13, 110, 253, 0.5));
    }
    
    .stat-content {
      flex: 1;
    }
    
    .stat-value {
      font-size: 1.5rem;
      font-weight: 800;
      margin-bottom: 0.1rem;
      color: #fff;
    }
    
    .stat-label {
      font-size: 0.75rem;
      color: rgba(255, 255, 255, 0.6);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    /* Tables */
    .table-container {
      overflow-x: auto;
    }
    
    .table {
      width: 100%;
      color: #fff !important;
      border-collapse: separate;
      border-spacing: 0 0.25rem;
    }
    
    .table thead th {
      border: none;
      color: #fff !important;
      background: rgba(255, 255, 255, 0.05);
      font-size: 0.75rem;
      text-transform: uppercase;
      padding: 0.75rem 0.5rem;
      font-weight: 700;
    }
    
    .table thead th:first-child { border-radius: 0.5rem 0 0 0.5rem; }
    .table thead th:last-child { border-radius: 0 0.5rem 0.5rem 0; }
    
    .table td {
      border: none;
      padding: 0.75rem 0.5rem;
      vertical-align: middle;
      font-size: 0.85rem;
      background: rgba(255, 255, 255, 0.02);
      color: #fff !important;
    }
    
    .table td span, .table td strong {
      color: inherit !important;
    }

    .table .text-primary {
      color: var(--accent-color) !important;
    }
    
    /* Badges */
    .badge-custom {
      padding: 0.3rem 0.5rem;
      border-radius: 0.4rem;
      font-size: 0.65rem;
      font-weight: 600;
      text-transform: uppercase;
    }
    
    .bg-success-soft { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
    .bg-warning-soft { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
    .bg-danger-soft { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }

    /* Lists */
    .event-item {
      padding: 0.75rem;
      border-radius: 0.6rem;
      background: rgba(255, 255, 255, 0.03);
      margin-bottom: 0.5rem;
      border-left: 3px solid var(--accent-color);
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
      <a href="#" class="sidebar-brand">
        <i class="bi bi-shield-lock-fill"></i>
        <span>Prison Admin</span>
      </a>
    </div>
    
    <div class="sidebar-menu">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a href="#" class="nav-link active">
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
    <!-- Top Header -->
    <div class="top-header">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-link d-lg-none sidebar-toggler p-0">
          <i class="bi bi-list fs-2"></i>
        </button>
        <div class="page-title">
          <h1>Dashboard Overview</h1>
        </div>
      </div>
      
      <div class="user-menu dropdown">
        <div class="user-avatar" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          Admin
        </div>
        <ul class="dropdown-menu dropdown-menu-end app-card p-2" aria-labelledby="userDropdown">
          <li><a class="dropdown-item nav-link py-2" href="#"><i class="bi bi-person-circle me-2"></i>Profile</a></li>
          <li><a class="dropdown-item nav-link py-2" href="#"><i class="bi bi-gear-fill me-2"></i>Settings</a></li>
          <li><hr class="dropdown-divider border-secondary opacity-25"></li>
          <li><a class="dropdown-item nav-link py-2 text-danger fw-bold" href="../backend/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
    </div>
    
    <div class="row g-3">
      <!-- Main Column -->
      <div class="col-lg-8">
        <!-- Stats Cards -->
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="app-card stat-metric">
              <i class="bi bi-people stat-icon"></i>
              <div class="stat-content">
                <div class="stat-value">200</div>
                <div class="stat-label">Registered Prisoners</div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="app-card stat-metric">
              <i class="bi bi-shield-check stat-icon" style="color: #0ea5e9;"></i>
              <div class="stat-content">
                <div class="stat-value text-info">21</div>
                <div class="stat-label">Staff On Duty</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="app-card">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0"><i class="bi bi-activity text-accent"></i>Recent Activity</h5>
            <div class="btn-group">
              <button class="btn btn-sm btn-outline-secondary active" style="font-size: 0.7rem;">Today</button>
              <button class="btn btn-sm btn-outline-secondary" style="font-size: 0.7rem;">Week</button>
            </div>
          </div>
          
          <div class="table-container">
            <table class="table mb-0">
              <thead>
                <tr>
                  <th>Time</th>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>10:45 AM</td>
                  <td><span class="text-primary">#PR-1001</span></td>
                  <td>John Doe</td>
                  <td><span class="badge-custom bg-success-soft">Done</span></td>
                </tr>
                <tr>
                  <td>10:30 AM</td>
                  <td><span class="text-primary">#PR-1045</span></td>
                  <td>Mike Johnson</td>
                  <td><span class="badge-custom bg-warning-soft">Live</span></td>
                </tr>
                <tr>
                  <td>10:15 AM</td>
                  <td><span class="text-primary">#PR-1098</span></td>
                  <td>Sarah Williams</td>
                  <td><span class="badge-custom bg-success-soft">Done</span></td>
                </tr>
                <tr>
                  <td>09:50 AM</td>
                  <td><span class="text-primary">#PR-1123</span></td>
                  <td>David Brown</td>
                  <td><span class="badge-custom bg-danger-soft">Missed</span></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mt-3 text-center">
            <button class="btn btn-link text-primary text-decoration-none fw-600 p-0" style="font-size: 0.8rem;">
              View All <i class="bi bi-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Right Column -->
      <div class="col-lg-4">
        <!-- Schedule -->
        <div class="app-card">
          <h5 class="card-title" style="color: #fff !important;"><i class="bi bi-calendar-check text-warning"></i>Schedule</h5>
          <div class="event-item">
            <div class="d-flex justify-content-between mb-0">
              <strong style="font-size: 0.8rem; color: #fff !important;">Parole Hearing</strong>
              <span class="text-accent" style="font-size: 0.75rem;">10:00</span>
            </div>
            <p class="mb-0 text-muted" style="font-size: 0.7rem; color: rgba(255,255,255,0.6) !important;">PR-1001 - RM 3</p>
          </div>
          <div class="event-item" style="border-left-color: #10b981;">
            <div class="d-flex justify-content-between mb-0">
              <strong style="font-size: 0.8rem; color: #fff !important;">Medical Group</strong>
              <span class="text-accent" style="font-size: 0.75rem;">11:30</span>
            </div>
            <p class="mb-0 text-muted" style="font-size: 0.7rem; color: rgba(255,255,255,0.6) !important;">Block B - Medical</p>
          </div>
          <div class="event-item" style="border-left-color: #f59e0b;">
            <div class="d-flex justify-content-between mb-0">
              <strong style="font-size: 0.8rem; color: #fff !important;">Orientation</strong>
              <span class="text-accent" style="font-size: 0.75rem;">14:00</span>
            </div>
            <p class="mb-0 text-muted" style="font-size: 0.7rem; color: rgba(255,255,255,0.6) !important;">Main Lobby</p>
          </div>
        </div>
        
        <!-- Quick Actions -->
          <h5 class="card-title" style="color: #fff !important;"><i class="bi bi-lightning-fill text-info"></i>Quick Actions</h5>
          <div class="d-grid gap-2">
            <a href="dashboard-warden.php" target="_blank" class="btn btn-outline-primary text-start p-2 d-flex align-items-center" style="font-size: 0.8rem; text-decoration: none;">
              <i class="bi bi-person-plus-fill me-2"></i> Add Prisoner
            </a>
            <button class="btn btn-outline-light text-start p-2 d-flex align-items-center border-opacity-25" style="background: rgba(255,255,255,0.02); font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#exportModal">
              <i class="bi bi-file-earmark-bar-graph me-2"></i> Export Report
            </button>
            <button class="btn btn-outline-danger text-start p-2 d-flex align-items-center bg-danger bg-opacity-10" style="font-size: 0.8rem;" onclick="alert('CRITICAL ERROR: The attendance monitoring system has encountered an internal communication failure. Please verify device connectivity and server logs immediately.')">
              <i class="bi bi-exclamation-octagon-fill me-2"></i> EMERGENCY
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
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
  <!-- Export Modal -->
  <div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); color: #fff;">
        <div class="modal-header border-bottom-0 pb-0">
          <h5 class="modal-title font-weight-bold">Generate Admin Report</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-4">
          <p class="text-muted small mb-4">Select the operational directory you wish to export as a professional PDF document.</p>
          <div class="d-grid gap-3">
            <button onclick="automatedExport('staff')" class="btn btn-outline-primary d-flex align-items-center justify-content-between p-3 rounded-3" style="border-width: 2px;">
              <div>
                <i class="bi bi-person-badge-fill me-2 fs-5"></i>
                <span class="fw-bold">Staff Directory</span>
              </div>
              <i class="bi bi-download"></i>
            </button>
            <button onclick="automatedExport('prisoners')" class="btn btn-outline-info d-flex align-items-center justify-content-between p-3 rounded-3" style="border-width: 2px;">
              <div>
                <i class="bi bi-people-fill me-2 fs-5"></i>
                <span class="fw-bold">Prisoner Roster</span>
              </div>
              <i class="bi bi-download"></i>
            </button>
          </div>
        </div>
        <div class="modal-footer border-top-0 pt-0">
          <p class="w-100 text-center small text-muted mb-0">Confidential Administration Data</p>
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
    async function automatedExport(type) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
        const btn = event.currentTarget;
        const originalHtml = btn.innerHTML;
        
        try {
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2 pulse"></i> Generating...';
            btn.disabled = true;

            const response = await fetch(`../backend/get_export_data.php?type=${type}`);
            const data = await response.json();
            
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');
            const now = new Date();
            const timestamp = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();

            // Styling variables
            const title = type === 'staff' ? "ADMINISTRATIVE STAFF DIRECTORY" : "CENTRAL PRISONER ROSTER";
            const headerColor = type === 'staff' ? [13, 110, 253] : [15, 23, 42];
            
            // PDF Header
            doc.setFillColor(...headerColor);
            doc.rect(0, 0, 297, 22, 'F');
            doc.setTextColor(255, 255, 255);
            doc.setFontSize(20);
            doc.text(title, 14, 15);
            doc.setFontSize(9);
            doc.text(`Official System Report | ${timestamp}`, 220, 14);

            // Table Prep
            const columns = type === 'staff' 
                ? [["ID", "Name", "Role", "Designation", "Status", "Wing", "Shift"]]
                : [["ID", "Full Name", "Wing", "Cell", "Crime", "Admission", "Release"]];
            
            const rows = data.map(item => type === 'staff'
                ? [item.user_id, item.full_name, item.role, item.designation, item.status, item.assigned_wing, item.shift_type]
                : [item.prisoner_id, item.full_name, item.block_wing, item.cell_number, item.crime, item.admission_date, item.expected_release]
            );

            // Generate Table
            doc.autoTable({
                head: columns,
                body: rows,
                startY: 28,
                theme: 'striped',
                headStyles: { fillColor: headerColor, fontSize: 10, halign: 'center' },
                bodyStyles: { fontSize: 8.5 },
                alternateRowStyles: { fillColor: [245, 247, 250] },
                margin: { left: 14, right: 14 }
            });

            // Auto-Download
            doc.save(`${type.charAt(0).toUpperCase() + type.slice(1)}_Report_${now.getTime()}.pdf`);
            modal.hide();
        } catch (error) {
            console.error('Export Error:', error);
            alert('Failed to generate report. Please try again.');
        } finally {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
      const sidebar = document.querySelector('.sidebar');
      const toggler = document.querySelector('.sidebar-toggler');
      
      if(toggler) {
        toggler.addEventListener('click', () => {
          sidebar.classList.toggle('show');
        });
      }

      // Close sidebar if clicking outside on mobile
      document.addEventListener('click', (e) => {
        if (window.innerWidth <= 992) {
          if (!sidebar.contains(e.target) && !toggler.contains(e.target) && sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
          }
        }
      });
    });
  </script>
</body>
</html>
