<?php
require_once '../backend/session_start.php';
if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'guard') {
    header("Location: index.php");
    exit();
}

require_once '../backend/db_connect.php';
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$guard = $stmt->get_result()->fetch_assoc();

// Auto-start Python script if not running (Local Mode)
$python_port = 5000;
$connection = @fsockopen('127.0.0.1', $python_port);

if (!$connection) {
    // Port is closed, try to start the script
    $python_path = "python"; // Ensure python is in system PATH
    $script_path = realpath('../python/face_attendance.py');
    
    // Windows background command: start /B hide the window
    if (stristr(PHP_OS, 'WIN')) {
        pclose(popen("start /B $python_path \"$script_path\" > nul 2>&1", "r"));
    } else {
        exec("$python_path \"$script_path\" > /dev/null 2>&1 &");
    }
} else {
    fclose($connection);
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

<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

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

.table-modern {
  border-collapse: separate;
  border-spacing: 0 8px;
}

.table-modern tr {
  background: rgba(30, 41, 59, 0.4);
  transition: all 0.2s ease;
}

.table-modern tr:hover {
  background: rgba(30, 41, 59, 0.6);
  transform: scale(1.005);
}

.table-modern td {
  border: none !important;
  padding: 16px !important;
}

.table-modern td:first-child { border-radius: 8px 0 0 8px; }
.table-modern td:last-child { border-radius: 0 8px 8px 0; }

  .btn-report-outline {
  border: 1px solid rgba(248, 81, 73, 0.4);
  color: #f85149;
  background: transparent;
  font-size: 0.7rem;
  padding: 2px 10px;
  border-radius: 4px;
}

.shift-card {
  transition: all 0.2s ease;
  cursor: pointer;
  background: rgba(30, 41, 59, 0.4);
}

.shift-card:hover {
  background: rgba(30, 41, 59, 0.6);
  transform: translateY(-2px);
}

.shift-card {
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 140px; /* Force minimum height for consistency */
}

.shift-btn-entry {
  background: rgba(16, 185, 129, 0.1);
  border: 1px solid var(--status-dot-green);
  color: var(--status-dot-green);
  font-size: 0.75rem;
  font-weight: 600;
  padding: 6px 15px;
  border-radius: 4px;
}

.shift-btn-exit {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid var(--status-red);
  color: var(--status-red);
  font-size: 0.75rem;
  font-weight: 600;
  padding: 6px 15px;
  border-radius: 4px;
}

.notification-toast {
  position: fixed;
  top: 100px;
  right: 24px;
  z-index: 9999;
  min-width: 320px;
  background: #1e293b;
  border: 1px solid var(--border-color);
  border-left: 5px solid var(--accent-cyan);
  border-radius: 8px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(10px);
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

      <div class="dropdown">
        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center text-white text-decoration-none" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; color: white;">
            <i class="bi bi-person-fill"></i>
          </div>
          <span>Profile</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow-lg" aria-labelledby="profileDropdown">
          <li><a class="dropdown-item" href="dashboard-guard.php"><i class="bi bi-speedometer2 me-2"></i> Main Page</a></li>
          <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-circle me-2"></i> Profile</a></li>
          <li><hr class="dropdown-divider border-secondary"></li>
          <li><a class="dropdown-item text-danger fw-bold" href="../backend/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<!-- MAIN -->
<div class="container-fluid px-4" style="margin-top:90px; margin-bottom:40px;">

  <div class="row g-4">

    <!-- LEFT COLUMN -->
    <div class="col-lg-4 col-xl-3">

      <!-- Guard Profile (Moved Above) -->
      <div class="app-card mb-4 overflow-hidden">
        <div class="card-header-gray d-flex justify-content-between align-items-center">
          <span class="fw-medium small text-uppercase">Guard Profile</span>
          <span class="badge bg-primary rounded-pill px-2 py-1" style="font-size: 0.6rem;">Active</span>
        </div>

        <div class="p-4 text-center">
          <div class="position-relative d-inline-block mb-3">
             <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #1e293b, #0f172a); border: 2px solid var(--accent-cyan); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; overflow: hidden; box-shadow: 0 0 15px rgba(34, 211, 238, 0.2);">
                <i class="bi bi-person-fill text-info fs-1"></i>
             </div>
          </div>
          
          <h5 class="mb-1 fw-bold"><?php echo htmlspecialchars($guard['full_name']); ?></h5>
          <p class="text-secondary small mb-3">ID: <?php echo htmlspecialchars($guard['user_id']); ?></p>

          <div class="row g-0 pt-3 border-top border-secondary border-opacity-25">
            <div class="col-6 text-start">
              <div class="text-secondary small text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Designation</div>
              <div class="fw-bold small"><?php echo htmlspecialchars($guard['designation']); ?></div>
            </div>
            <div class="col-6 text-end">
              <div class="text-secondary small text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Shift</div>
              <div class="fw-bold small"><?php echo htmlspecialchars($guard['shift_type'] ?? 'Day Shift'); ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Device Status (Moved Below) -->
      <div class="app-card mb-4">
        <div class="p-4">
          <h6 class="text-uppercase text-secondary small fw-bold mb-4">Device Status</h6>
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="scanner-icon-border">
              <i class="bi bi-camera-video fs-4"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-bold">Face Recognition</h5>
              <small class="text-secondary" id="recogStatus">Monitoring Inactive</small>
            </div>
          </div>
          <button class="btn btn-info btn-sm w-100 fw-bold" onclick="launchRecognition()">
            <i class="bi bi-play-fill me-1"></i>Launch Recognition
          </button>
          <div class="mt-2 small text-secondary">
            Note: Ensure Python script is running locally.
          </div>
        </div>
      </div>

      <!-- Device Status remains as is -->

    </div>

    <!-- RIGHT COLUMN -->
    <div class="col-lg-8 col-xl-9">

      <!-- Shift Control Center -->
      <div class="row g-3 mb-4">
        <?php 
        $shifts = ['Breakfast', 'Laundry', 'Lunch', 'Dinner'];
        $icons = ['coffee', 'clock', 'egg-fried', 'moon-stars'];
        foreach($shifts as $index => $shift): ?>
        <div class="col-md-3">
          <div class="app-card p-3 shift-card">
            <div class="d-flex align-items-center gap-2 mb-3">
              <i class="bi bi-<?php echo $icons[$index]; ?> text-info fs-5"></i>
              <h6 class="mb-0 fw-bold"><?php echo $shift; ?></h6>
            </div>
            <div class="d-flex gap-2">
              <button class="shift-btn-entry flex-grow-1" onclick="openScanner('<?php echo $shift; ?>', 'Entry')">Entry</button>
              <button class="shift-btn-exit flex-grow-1" onclick="openScanner('<?php echo $shift; ?>', 'Exit')">Exit</button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

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
            <table class="table table-modern align-middle">
              <thead>
                <tr>
                  <th width="12%">TIME</th>
                  <th width="15%">ID</th>
                  <th width="30%">PRISONER NAME</th>
                  <th width="13%">CELL</th>
                  <th width="20%">SHIFT & MOVEMENT</th>
                  <th width="10%">ACTION</th>
                </tr>
              </thead>
              <tbody id="attendanceLogBody">
                <!-- Data will be loaded via AJAX -->
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

<!-- Modal for Face Recognition Scanner -->
<div class="modal fade" id="scannerModal" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark border-secondary">
      <div class="modal-header border-secondary">
        <h5 class="modal-title fw-bold" id="scannerTitle">Face Recognition Monitor</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="stopScanner()"></button>
      </div>
      <div class="modal-body p-0 overflow-hidden bg-black d-flex align-items-center justify-content-center" style="aspect-ratio: 4/3; max-height: 70vh;">
        <div id="scannerMessage" class="position-absolute d-flex flex-column align-items-center justify-content-center p-5 w-100 h-100">
            <div class="spinner-border text-info mb-3" role="status"></div>
            <p class="text-secondary">Initializing camera feed...</p>
        </div>
        <img id="videoFeed" src="" class="w-100 h-100 d-none" style="object-fit: cover; display: block;">
      </div>
      <div class="modal-footer border-secondary justify-content-between">
        <div class="text-info small fw-bold">
            <span class="status-dot-sm bg-info"></span> <span id="activeContext">Lunch - Entry</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-info btn-sm" onclick="reloadData()">
                <i class="bi bi-arrow-clockwise me-1"></i>Reload Data
            </button>
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="stopScanner()">Close Monitor</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-white border-secondary">
      <div class="modal-header border-secondary">
        <h5 class="modal-title"><i class="bi bi-bell-fill me-2 text-warning"></i>New Notification</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="notificationBody">
        <!-- Notification message will be injected here -->
      </div>
      <div class="modal-footer border-secondary">
        <button type="button" class="btn btn-primary btn-sm" id="markReadBtn">Mark as Read</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast Container -->
<div id="toastContainer"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
let scannerActive = false;
let lastAttendanceId = null;

function openScanner(shift, type) {
    document.getElementById('scannerTitle').innerText = `${shift} ${type} - Active Monitor`;
    document.getElementById('activeContext').innerText = `${shift} - ${type}`;
    
    // Set Context in Python
    fetch('http://localhost:5000/set_context', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({shift: shift, type: type})
    }).then(() => {
        const modal = new bootstrap.Modal(document.getElementById('scannerModal'));
        modal.show();
        
        const video = document.getElementById('videoFeed');
        const msg = document.getElementById('scannerMessage');
        
        video.src = "http://localhost:5000/video_feed";
        video.classList.remove('d-none');
        msg.classList.add('d-none');
        
        scannerActive = true;
    }).catch(err => {
        alert("Face Recognition Server not running! It should auto-start, or run 'face_attendance.py' manually.");
    });
}

function stopScanner() {
    const video = document.getElementById('videoFeed');
    video.src = "";
    video.classList.add('d-none');
    document.getElementById('scannerMessage').classList.remove('d-none');
    scannerActive = false;
}

function reloadData() {
    const btn = event.currentTarget;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i>Reloading...';
    btn.disabled = true;

    fetch('http://localhost:5000/reload_data', {
        method: 'POST'
    }).then(r => r.json())
    .then(data => {
        alert("Face data reloaded! " + data.count + " records processed.");
    }).catch(err => {
        alert("Failed to connect to Python server.");
    }).finally(() => {
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
}

function showNotification(entry) {
    const container = document.getElementById('toastContainer');
    const toastId = 'toast-' + Date.now();
    
    const toastHTML = `
        <div class="notification-toast p-3 rounded shadow animate__animated animate__fadeInRight" id="${toastId}">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle overflow-hidden border border-info" style="width: 48px; height: 48px;">
                    <img src="${entry.photo_path}" class="w-100 h-100" style="object-fit: cover;">
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold text-info" style="font-size: 0.8rem;">${entry.movement_type} MARKED</div>
                    <div class="fw-bold">${entry.full_name}</div>
                    <small class="text-secondary">Cell: ${entry.cell_number} | ${entry.shift_name}</small>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', toastHTML);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        const el = document.getElementById(toastId);
        if (el) el.remove();
    }, 5000);
}

function checkServerStatus() {
    fetch('http://localhost:5000/ping')
    .then(() => {
        document.getElementById('recogStatus').innerText = "System Online";
        document.getElementById('recogStatus').classList.remove('text-secondary');
        document.getElementById('recogStatus').classList.add('text-info');
    }).catch(() => {
        document.getElementById('recogStatus').innerText = "Initializing...";
        document.getElementById('recogStatus').classList.add('text-secondary');
        document.getElementById('recogStatus').classList.remove('text-info');
    });
}

// Check every 5 seconds
setInterval(checkServerStatus, 5000);
checkServerStatus();

function launchRecognition() {
    alert("Python system is auto-started! Just click any shift button to open the scanner.");
}

function updateAttendanceLog() {
    fetch('../backend/get_attendance_log.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('attendanceLogBody');
            
            if (data.length > 0) {
                // Check for new detections for notification
                const latest = data[0];
                const currentId = latest.time_in + '_' + latest.prisoner_id + '_' + latest.movement_type + '_' + latest.shift_name;
                
                // If lastAttendanceId is null, it's the first load - populate without toast
                if (lastAttendanceId === null) {
                   lastAttendanceId = currentId;
                   console.log("Initial Load - Syncing with ID:", currentId);
                } else if (lastAttendanceId !== currentId) {
                    console.log("NEW DETECTION! ID:", currentId);
                    showNotification(latest);
                    lastAttendanceId = currentId;
                }

                // Update UI only if data actually changed or if it's the first time
                // To be safe, we'll just rebuild the body for now
                tbody.innerHTML = '';
                
                // Update Table
                data.forEach(entry => {
                    const row = `
                        <tr>
                            <td class="small fw-bold text-secondary">${entry.time_in}</td>
                            <td class="text-info fw-bold">${entry.prisoner_id}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle overflow-hidden border border-secondary" style="width:32px; height:32px; background: #1e293b;">
                                        <img src="${entry.photo_path}" class="w-100 h-100" style="object-fit: cover;">
                                    </div>
                                    <span class="fw-medium">${entry.full_name}</span>
                                </div>
                            </td>
                            <td>${entry.cell_number}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="small text-secondary mb-1">${entry.shift_name}</span>
                                    <span style="color: ${entry.movement_type === 'Entry' ? 'var(--status-dot-green)' : 'var(--status-red)'}; font-size: 0.8rem; font-weight: 700;">
                                        <span class="status-dot-sm" style="background: ${entry.movement_type === 'Entry' ? 'var(--status-dot-green)' : 'var(--status-red)'};"></span>${entry.movement_type.toUpperCase()}
                                    </span>
                                </div>
                            </td>
                            <td><button class="btn btn-sm btn-dark border-secondary"><i class="bi bi-three-dots"></i></button></td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            } else {
                if (tbody.innerHTML === '') {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-secondary py-5">No attendance recorded today</td></tr>';
                }
            }
        })
        .catch(error => console.error('Error fetching attendance:', error));
}

function checkNotifications() {
    fetch('../backend/get_notifications.php')
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success' && data.data.length > 0) {
                const notif = data.data[0]; 
                const modalEl = document.getElementById('notificationModal');
                const modal = new bootstrap.Modal(modalEl);
                document.getElementById('notificationBody').innerText = notif.message;
                
                document.getElementById('markReadBtn').onclick = function() {
                    const formData = new FormData();
                    formData.append('id', notif.id);
                    fetch('../backend/mark_notification_read.php', {
                        method: 'POST',
                        body: formData
                    }).then(() => {
                        modal.hide();
                        setTimeout(checkNotifications, 1000);
                    });
                };
                
                modal.show();
            }
        })
        .catch(e => console.error('Error fetching notifications:', e));
}

// Poll every 2 seconds for faster notifications
setInterval(updateAttendanceLog, 2000);
// Check for leave notifications on load
checkNotifications();
// Initial load
updateAttendanceLog();
</script>

</body>
</html>
