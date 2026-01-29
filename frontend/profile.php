<?php
require_once '../backend/session_start.php';
require_once '../backend/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

$role = strtolower($user['role']);
$dashboard_link = "dashboard-$role.php";
if ($role === 'admin') $dashboard_link = "dashboard-admin.php";

// Fetch alternative staff members (same role, excluding current user)
$alt_stmt = $conn->prepare("SELECT user_id, full_name, designation FROM users WHERE role = ? AND user_id != ?");
$alt_stmt->bind_param("ss", $user['role'], $user_id);
$alt_stmt->execute();
$alt_staff_result = $alt_stmt->get_result();
$alt_staff_list = [];
while ($row = $alt_staff_result->fetch_assoc()) {
    $alt_staff_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Profile – Biometric Attendance System</title>
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
    .navbar {
      background: rgba(15, 23, 42, 0.95);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .navbar-brand {
      color: #f8f9fa !important;
      font-weight: 600;
    }
    .app-card {
      border-radius: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background: rgba(15, 23, 42, 0.8);
      backdrop-filter: blur(12px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .form-control {
      background-color: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: #f8f9fa;
      border-radius: 0.5rem;
    }
    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.1);
      border-color: #0d6efd;
      color: #fff;
      box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.4);
    }
    .profile-label {
      color: rgba(255, 255, 255, 0.5);
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .profile-value {
      font-weight: 500;
      color: #fff;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand" href="<?php echo $dashboard_link; ?>">
        <i class="bi bi-shield-lock me-2"></i>Prison Monitor
      </a>
      <div class="ms-auto d-flex align-items-center">
        <div class="dropdown">
          <a href="#" class="btn btn-link text-white text-decoration-none dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
              <i class="bi bi-person-fill"></i>
            </div>
            <span class="d-none d-sm-inline"><?php echo htmlspecialchars($user['full_name']); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow-lg">
            <li><a class="dropdown-item" href="<?php echo $dashboard_link; ?>"><i class="bi bi-speedometer2 me-2"></i> Main Page</a></li>
            <li><a class="dropdown-item active" href="profile.php"><i class="bi bi-person-circle me-2"></i> Profile</a></li>
            <li><hr class="dropdown-divider border-secondary"></li>
            <li><a class="dropdown-item text-danger" href="../backend/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <div class="container" style="margin-top: 80px; margin-bottom: 40px;">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        
        <div class="row g-4">
          <!-- Account Details Card -->
          <div class="col-md-4">
            <div class="app-card p-4 text-center">
              <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 100px; height: 100px; font-size: 3rem;">
                <i class="bi bi-person-fill"></i>
              </div>
              <h4 class="mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h4>
              <p class="text-info small mb-3"><?php echo strtoupper($user['role']); ?></p>
              <div class="badge bg-success opacity-75 px-3 py-2"><?php echo $user['status']; ?></div>
              
              <hr class="my-4 border-secondary opacity-25">
              
              <div class="text-start">
                <div class="mb-3">
                  <div class="profile-label">User ID</div>
                  <div class="profile-value"><?php echo htmlspecialchars($user['user_id']); ?></div>
                </div>
                <div class="mb-3">
                  <div class="profile-label">Joining Date</div>
                  <div class="profile-value"><?php echo date('M d, Y', strtotime($user['joining_date'])); ?></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Profile Card -->
          <div class="col-md-8">
            <div class="app-card p-4 p-md-5">
              <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary border-opacity-25 pb-3">
                <h5 class="mb-0">
                  <i class="bi bi-pencil-square me-2 text-info"></i>Manage Details
                </h5>
                <button type="button" class="btn btn-warning btn-sm" onclick="toggleLeaveSection()">
                  <i class="bi bi-calendar-plus me-1"></i> Apply for Leave
                </button>
              </div>
              
              <form id="updateProfileForm" onsubmit="handleUpdate(event)">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label small text-secondary">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small text-secondary">Contact Extension</label>
                    <input type="text" name="contact_ext" class="form-control" value="<?php echo htmlspecialchars($user['contact_ext']); ?>">
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label small text-secondary">Designation</label>
                    <input type="text" class="form-control opacity-75" value="<?php echo htmlspecialchars($user['designation']); ?>" readonly>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small text-secondary">Assigned Wing</label>
                    <input type="text" class="form-control opacity-75" value="<?php echo htmlspecialchars($user['assigned_wing']); ?>" readonly>
                  </div>
                  
                  <div class="col-md-12">
                     <label class="form-label small text-secondary">Shift Details</label>
                     <input type="text" class="form-control opacity-75" value="<?php echo htmlspecialchars($user['shift_type']); ?>" readonly>
                  </div>

                  <div class="col-12 mt-4 pt-3 border-top border-secondary border-opacity-25">
                    <h6 class="mb-3">Change Password</h6>
                    <div class="row g-3">
                      <div class="col-md-6">
                        <input type="password" name="new_password" class="form-control" placeholder="New Password">
                      </div>
                      <div class="col-md-6">
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                      </div>
                    </div>
                  </div>

                  <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-5" id="saveBtn">
                      Save Changes
                    </button>
                  </div>
                </div>
              </form>
            </div>
            
            <!-- Leave Application Card -->
            <div id="leaveSection" class="app-card p-4 p-md-5 mt-4" style="display: none;">
              <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary border-opacity-25 pb-3">
                <h5 class="mb-0">
                  <i class="bi bi-calendar-check me-2 text-warning"></i>Apply for Leave
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="toggleLeaveSection()" aria-label="Close"></button>
              </div>
              
              <form id="applyLeaveForm" onsubmit="handleApplyLeave(event)">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label small text-secondary">Leave Date</label>
                    <input type="date" name="leave_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small text-secondary">Shift Type</label>
                    <select name="shift" class="form-select" required>
                      <option value="" disabled selected>Select Shift</option>
                      <option value="Day Shift">Day Shift</option>
                      <option value="Night Shift">Night Shift</option>
                      <option value="General">General</option>
                    </select>
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label small text-secondary">Duration</label>
                    <div class="d-flex gap-4 mt-2">
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="duration" value="Full Day" id="fullDay" checked>
                        <label class="form-check-label" for="fullDay">Full Day</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="duration" value="Half Day" id="halfDay">
                        <label class="form-check-label" for="halfDay">Half Day</label>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label small text-secondary">Alternative Arrangement By</label>
                    <select name="alt_staff_id" class="form-select" required>
                      <option value="" disabled selected>Select Staff</option>
                      <?php foreach ($alt_staff_list as $staff): ?>
                        <option value="<?php echo htmlspecialchars($staff['user_id']); ?>">
                          <?php echo htmlspecialchars($staff['full_name']); ?> (<?php echo htmlspecialchars($staff['designation']); ?>)
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="col-12">
                    <label class="form-label small text-secondary">Reason for Leave</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Explain the reason for leave..." required></textarea>
                  </div>

                  <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-warning px-5" id="applyBtn">
                      <i class="bi bi-send me-2"></i>Submit Application
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    function handleUpdate(event) {
      event.preventDefault();
      const form = event.target;
      const formData = new FormData(form);
      const btn = document.getElementById('saveBtn');
      const originalText = btn.innerHTML;

      if (formData.get('new_password') !== formData.get('confirm_password')) {
        alert('Passwords do not match');
        return;
      }

      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

      fetch('../backend/update_profile.php', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        if (data.status === 'success') {
          alert('Profile updated successfully!');
          location.reload();
        } else {
          alert('Error: ' + data.message);
          btn.disabled = false;
          btn.innerHTML = originalText;
        }
      })
      .catch(e => {
        console.error(e);
        alert('An error occurred.');
        btn.disabled = false;
        btn.innerHTML = originalText;
      });
    }

    function handleApplyLeave(event) {
      event.preventDefault();
      const form = event.target;
      const formData = new FormData(form);
      const btn = document.getElementById('applyBtn');
      const originalText = btn.innerHTML;

      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

      fetch('../backend/apply_leave.php', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        if (data.status === 'success') {
          alert('Leave application submitted successfully!');
          form.reset();
          btn.disabled = false;
          btn.innerHTML = originalText;
        } else {
          alert('Error: ' + data.message);
          btn.disabled = false;
          btn.innerHTML = originalText;
        }
      })
      .catch(e => {
        console.error(e);
        alert('An error occurred.');
        btn.disabled = false;
        btn.innerHTML = originalText;
      });
    }
    function toggleLeaveSection() {
      const section = document.getElementById('leaveSection');
      if (section.style.display === 'none') {
        section.style.display = 'block';
        section.scrollIntoView({ behavior: 'smooth' });
      } else {
        section.style.display = 'none';
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('action') === 'apply_leave') {
        toggleLeaveSection();
      }
    });
  </script>
</body>
</html>
