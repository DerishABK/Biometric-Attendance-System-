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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="admin-style.css">
  <style>
    :root {
      --accent-color: #0d6efd;
    }
    /* Section Title (Page Specific) */

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

  <?php include 'admin-sidebar.php'; ?>

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

</body>
</html>
