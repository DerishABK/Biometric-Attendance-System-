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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Settings | Prison PASS</title>
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
      padding: 1.5rem;
      margin-bottom: 20px;
    }

    .section-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
      color: #f8fafc;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .form-label {
      color: #cbd5e1;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .text-muted { color: #94a3b8 !important; }
    .small.text-muted { color: #cbd5e1 !important; }

    .form-control, .form-select {
      background: rgba(0, 0, 0, 0.2);
      border: 1px solid var(--glass-border);
      color: #fff;
      font-size: 0.9rem;
    }

    .form-control:focus {
      background: rgba(0, 0, 0, 0.3);
      border-color: var(--accent-color);
      color: #fff;
      box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.1);
    }

    .form-check-input {
      background-color: rgba(255, 255, 255, 0.1);
      border-color: var(--glass-border);
    }

    .form-check-input:checked {
      background-color: var(--accent-color);
      border-color: var(--accent-color);
    }

    .btn-save {
      background: var(--accent-color);
      border: none;
      padding: 0.5rem 1.5rem;
      font-weight: 600;
      font-size: 0.9rem;
    }

    .btn-outline-custom {
      border: 1px solid var(--glass-border);
      color: var(--text-muted);
      font-size: 0.85rem;
    }

    .btn-outline-custom:hover {
      background: rgba(255, 255, 255, 0.05);
      color: #fff;
    }

    .status-indicator {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 5px;
    }

    .status-online { background: #10b981; box-shadow: 0 0 10px rgba(16, 185, 129, 0.4); }

    /* Responsive */
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
          <a href="admin-visitation.php" class="nav-link">
            <i class="bi bi-calendar-event-fill"></i>
            <span>Visitation</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="admin-settings.php" class="nav-link active">
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
          <h1 class="h4 font-weight-bold mb-1">System Settings</h1>
          <p class="text-muted small mb-0">Configure your prison management environment</p>
        </div>
      </div>
      <button class="btn btn-primary btn-save">
        <i class="bi bi-check2-circle me-1"></i> Save Changes
      </button>
    </div>

    <div class="row">
      <!-- System Configuration -->
      <div class="col-lg-8">
        <div class="glass-card">
          <div class="section-title">
            <i class="bi bi-cpu-fill text-primary"></i> System Configuration
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Facility Name</label>
              <input type="text" class="form-control" value="Central Correctional Facility">
            </div>
            <div class="col-md-6">
              <label class="form-label">System Mode</label>
              <select class="form-select">
                <option selected>Standard Operational</option>
                <option>Restricted Access (Lockdown)</option>
                <option>Maintenance Mode</option>
              </select>
            </div>
            <div class="col-12">
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" id="backupSwitch" checked>
                <label class="form-check-label text-muted small" for="backupSwitch">Automated Daily Database Backups</label>
              </div>
            </div>
          </div>
        </div>

        <div class="glass-card">
          <div class="section-title">
            <i class="bi bi-shield-check text-success"></i> Security & Access
          </div>
          <div class="row g-4">
            <div class="col-md-6">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small">Two-Factor Authentication</span>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" checked>
                </div>
              </div>
              <p class="text-muted x-small mb-0" style="font-size: 0.75rem;">Require a secure code for all administrative logins</p>
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small">Session Timeout</span>
                <select class="form-select form-select-sm w-auto border-0 bg-dark text-white">
                  <option>15 Minutes</option>
                  <option selected>30 Minutes</option>
                  <option>1 Hour</option>
                </select>
              </div>
              <p class="text-muted x-small mb-0" style="font-size: 0.75rem;">Automatically log out inactive users</p>
            </div>
            <div class="col-12">
              <label class="form-label">Allowed IP Ranges (Whitelisting)</label>
              <input type="text" class="form-control" placeholder="192.168.1.0/24, 10.0.0.0/8">
            </div>
          </div>
        </div>

        <div class="glass-card">
          <div class="section-title">
            <i class="bi bi-bell-fill text-warning"></i> Notifications
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" checked>
                <label class="small">Critical Incident Alerts (SMS/Email)</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" checked>
                <label class="small">Staff Shift Reminders</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox">
                <label class="small">Weekly Compliance Reports</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" checked>
                <label class="small">Visitor Approval Notifications</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- System Status Sidebar -->
      <div class="col-lg-4">
        <div class="glass-card">
          <div class="section-title mb-3">
            <span class="status-indicator status-online"></span> System Status
          </div>
          <div class="d-flex flex-column gap-3">
            <div class="d-flex justify-content-between align-items-center">
              <span class="small text-muted">Server Load</span>
              <div class="progress w-50" style="height: 6px; background: rgba(255,255,255,0.05);">
                <div class="progress-bar bg-primary" style="width: 24%"></div>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="small text-muted">DB Latency</span>
              <span class="small text-success">14ms</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="small text-muted">Uptime</span>
              <span class="small text-white">99.98%</span>
            </div>
          </div>
          <hr class="opacity-10 my-3">
          <button class="btn btn-outline-custom w-100 py-2">
            <i class="bi bi-terminal me-2"></i> View System Logs
          </button>
        </div>

        <div class="glass-card bg-danger bg-opacity-10 border-danger border-opacity-25">
          <div class="section-title text-danger mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Danger Zone
          </div>
          <p class="x-small text-danger opacity-75 mb-3" style="font-size: 0.75rem;">Actions taken here are irreversible. Please proceed with caution.</p>
          <div class="d-grid gap-2">
            <button class="btn btn-outline-danger btn-sm">Clear System Cache</button>
            <button class="btn btn-outline-danger btn-sm">Reset All Permissions</button>
            <button class="btn btn-danger btn-sm">Factory Reset Database</button>
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
</body>
</html>
