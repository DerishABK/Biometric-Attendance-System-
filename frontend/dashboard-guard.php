<?php
require_once '../backend/session_start.php';
if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'guard') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Guard Dashboard – Attendance Monitor</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
  --bg-color: #05070a;
  --nav-bg: rgba(15, 23, 42, 0.9);
  --card-bg: rgba(15, 23, 42, 0.8);
  --header-dark: rgba(30, 41, 59, 0.5);
  --accent-cyan: #22d3ee;
  --status-green: #10b981;
  --status-dot-green: #34d399;
  --status-red: #ef4444;
  --text-primary: #f8f9fa;
  --text-secondary: #94a3b8;
  --border-color: rgba(255, 255, 255, 0.08);
}

body {
  min-height: 100vh;
  background: radial-gradient(circle at 20% 20%, #0f172a 0%, #05070a 100%);
  color: var(--text-primary);
  font-family: 'Inter', sans-serif;
}

/* Navbar */
.navbar {
  background: var(--nav-bg);
  border-bottom: 1px solid var(--border-color);
  padding: 0.75rem 0;
}

.badge-system {
  background-color: #238636;
  color: white;
  border-radius: 4px;
  padding: 4px 12px;
  font-size: 0.75rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 6px;
}

/* Cards */
.app-card {
  background-color: var(--card-bg);
  border: 1px solid var(--border-color);
  border-radius: 6px;
  overflow: hidden;
}

.card-header-gray {
  background-color: #21262d;
  padding: 10px 16px;
  border-bottom: 1px solid var(--border-color);
}

.search-input-dark {
  background-color: #0d1117;
  border: 1px solid var(--border-color);
  color: white;
  border-radius: 4px;
  padding: 4px 12px;
  width: 150px;
  font-size: 0.8rem;
}

/* Specific elements */
.scanner-icon-border {
  width: 44px;
  height: 44px;
  border: 1px solid rgba(34, 211, 238, 0.4);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--accent-cyan);
}

.prisoner-badge-outline {
  border: 1px solid var(--status-dot-green);
  color: var(--status-dot-green);
  background: rgba(63, 185, 80, 0.1);
  border-radius: 20px;
  padding: 2px 12px;
  font-size: 0.7rem;
  font-weight: 600;
}

.verified-btn-solid {
  background-color: #238636;
  color: white;
  border-radius: 20px;
  padding: 4px 18px;
  font-size: 0.75rem;
  font-weight: 600;
  border: none;
}

.table th {
  background-color: transparent;
  color: var(--text-secondary);
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.05em;
  padding: 12px 16px;
  border-bottom: 1px solid var(--border-color);
}

.table td {
  padding: 14px 16px;
  border-bottom: 1px solid rgba(48, 54, 61, 0.5);
  font-size: 0.85rem;
}

.status-dot-sm {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  display: inline-block;
  margin-right: 8px;
}

.btn-report-outline {
  border: 1px solid rgba(248, 81, 73, 0.4);
  color: #f85149;
  background: transparent;
  font-size: 0.7rem;
  padding: 2px 10px;
  border-radius: 4px;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container-fluid px-4">
    <a class="navbar-brand d-flex align-items-center text-white" href="#">
      <i class="bi bi-shield-check me-2"></i>Guard Monitor
    </a>

    <div class="ms-auto d-flex align-items-center gap-3">
      <div class="badge-system">
        <i class="bi bi-wifi"></i>System Online
      </div>

      <div class="dropdown">
        <button class="btn btn-dark btn-sm dropdown-toggle border-secondary" type="button" data-bs-toggle="dropdown">
          Block A - Gate 1
        </button>
        <ul class="dropdown-menu dropdown-menu-dark">
          <li><a class="dropdown-item" href="#">Block A - Gate 1</a></li>
          <li><a class="dropdown-item" href="#">Block B - Gate 2</a></li>
        </ul>
      </div>

      <a href="../backend/logout.php" class="btn btn-outline-danger btn-sm fw-bold">
      <i class="bi bi-box-arrow-right me-1"></i> Logout
    </a>
    </div>
  </div>
</nav>

<!-- MAIN -->
<div class="container-fluid px-4" style="margin-top:90px; margin-bottom:40px;">

  <div class="row g-4">

    <!-- LEFT COLUMN -->
    <div class="col-lg-4 col-xl-3">

      <!-- Scanner Status -->
      <div class="app-card mb-4">
        <div class="p-4">
          <h6 class="text-uppercase text-secondary small fw-bold mb-4">Device Status</h6>
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="scanner-icon-border">
              <i class="bi bi-fingerprint fs-4"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-bold">Scanner Active</h5>
              <small class="text-secondary">Ready for input...</small>
            </div>
          </div>
          <button class="btn-calibrate">
            <i class="bi bi-arrow-clockwise me-2"></i>Calibrate Sensor
          </button>
        </div>
      </div>

      <!-- Last Detected -->
      <div class="app-card">
        <div class="card-header-gray">
          <i class="bi bi-clock text-white opacity-75 me-2"></i>
          <span class="fw-medium small text-uppercase">Last Detected</span>
        </div>

        <div class="p-4 text-center">
          <div class="position-relative d-inline-block mb-3">
             <div class="avatar-container" style="width: 64px; height: 64px; background: #30363d; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-person-fill text-secondary fs-2"></i>
             </div>
             <span class="prisoner-badge-outline position-absolute top-0 start-50 translate-middle">
               Prisoner
             </span>
          </div>
          
          <h4 class="mb-1 fw-bold">John Doe</h4>
          <p class="text-secondary small mb-4">ID: P-2024-8821</p>

          <div class="mb-5">
            <button class="verified-btn-solid">
              <i class="bi bi-check-circle-fill me-2"></i>Verified
            </button>
          </div>

          <div class="row g-0 pt-3 border-top border-secondary border-opacity-25">
            <div class="col-6 text-start">
              <div class="text-secondary small text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Cell No</div>
              <div class="fw-bold">C-104</div>
            </div>
            <div class="col-6 text-end">
              <div class="text-secondary small text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Time</div>
              <div class="fw-bold">10:42:15 AM</div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN -->
    <div class="col-lg-8 col-xl-9">

      <div class="app-card h-100">
        <div class="p-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0 fw-bold">Live Attendance Log</h5>
            <div class="d-flex gap-2">
              <input type="text" class="search-input-dark" placeholder="">
              <button class="btn btn-dark btn-sm border-secondary px-2">
                <i class="bi bi-filter"></i>
              </button>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th width="15%">TIME</th>
                  <th width="20%">PRISONER ID</th>
                  <th width="25%">NAME</th>
                  <th width="15%">CELL NO</th>
                  <th width="15%">STATUS</th>
                  <th width="10%">ACTION</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>10:42:15 AM</td>
                  <td class="text-info fw-medium">P-2024-8821</td>
                  <td>
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-secondary bg-opacity-50 rounded-circle" style="width:28px; height:28px;"></div>
                      John Doe
                    </div>
                  </td>
                  <td>C-104</td>
                  <td>
                    <span style="color: var(--status-dot-green); font-weight: 600;">
                      <span class="status-dot-sm" style="background: var(--status-dot-green);"></span>Verified
                    </span>
                  </td>
                  <td><i class="bi bi-three-dots-vertical text-secondary"></i></td>
                </tr>
                <tr>
                  <td>10:41:03 AM</td>
                  <td class="text-info fw-medium">P-2023-1105</td>
                  <td>
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-secondary bg-opacity-50 rounded-circle" style="width:28px; height:28px;"></div>
                      Marcus Ray
                    </div>
                  </td>
                  <td>B-202</td>
                  <td>
                    <span style="color: var(--status-dot-green); font-weight: 600;">
                      <span class="status-dot-sm" style="background: var(--status-dot-green);"></span>Verified
                    </span>
                  </td>
                  <td><i class="bi bi-three-dots-vertical text-secondary"></i></td>
                </tr>
                <tr>
                  <td>10:38:55 AM</td>
                  <td class="text-danger fw-medium">UNKNOWN</td>
                  <td><span class="text-danger">--</span></td>
                  <td>--</td>
                  <td>
                    <span style="color: var(--status-red); font-weight: 600;">
                      <span class="status-dot-sm" style="background: var(--status-red);"></span>Failed
                    </span>
                  </td>
                  <td>
                    <button class="btn-report-outline">Report</button>
                  </td>
                </tr>
                <tr>
                  <td>10:35:20 AM</td>
                  <td class="text-info fw-medium">P-2024-5590</td>
                  <td>
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-secondary bg-opacity-50 rounded-circle" style="width:28px; height:28px;"></div>
                      Sarah Connor
                    </div>
                  </td>
                  <td>W-101</td>
                  <td>
                    <span style="color: var(--status-dot-green); font-weight: 600;">
                      <span class="status-dot-sm" style="background: var(--status-dot-green);"></span>Verified
                    </span>
                  </td>
                  <td><i class="bi bi-three-dots-vertical text-secondary"></i></td>
                </tr>
                <tr>
                  <td>10:30:12 AM</td>
                  <td class="text-info fw-medium">P-2022-3341</td>
                  <td>
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-secondary bg-opacity-50 rounded-circle" style="width:28px; height:28px;"></div>
                      Mike Ross
                    </div>
                  </td>
                  <td>A-005</td>
                  <td>
                    <span style="color: var(--status-dot-green); font-weight: 600;">
                      <span class="status-dot-sm" style="background: var(--status-dot-green);"></span>Verified
                    </span>
                  </td>
                  <td><i class="bi bi-three-dots-vertical text-secondary"></i></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="text-center mt-5 mb-2">
            <small class="text-secondary">Showing last 5 entries. <a href="#" class="text-info text-decoration-none ms-2">View Full Log</a></small>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
