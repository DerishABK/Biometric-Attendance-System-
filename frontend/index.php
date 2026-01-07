<?php
require_once '../backend/session_start.php';
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    // If we have a valid session, only redirect if NOT accessing with a logout/reset intent
    if (!isset($_GET['relogin'])) {
        $redirect = "";
        $role = strtolower(trim($_SESSION['role']));
        switch($role) {
            case 'admin': $redirect = "dashboard-admin.php"; break;
            case 'warden': $redirect = "dashboard-warden.php"; break;
            case 'guard': $redirect = "dashboard-guard.php"; break;
        }
        
        if ($redirect) {
            session_write_close();
            header("Location: $redirect");
            exit();
        }
    }
}
// Clear session if explicitly requested via URL
if (isset($_GET['clear_session'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Prisoner Attendance – Login</title>

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    body {
      min-height: 100vh;
      background: radial-gradient(circle at top left, #1e3a8a, #050505 60%);
      color: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }
    .login-card {
      border-radius: 1.2rem;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background: rgba(15, 23, 42, 0.8);
      backdrop-filter: blur(12px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
      max-width: 400px;
      width: 100%;
    }
    .form-control {
      background-color: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: #f8f9fa;
      border-radius: 0.7rem;
      padding: 0.75rem 1rem;
    }
    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.1);
      border-color: #0d6efd;
      color: #fff;
      box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.3);
    }
    .btn-primary {
      background-color: #0d6efd;
      border-color: #0d6efd;
      color: #fff;
      font-weight: 600;
      border-radius: 0.7rem;
      padding: 0.75rem;
    }
    .btn-primary:hover {
      background-color: #0b5ed7;
      border-color: #0b5ed7;
    }
    .fingerprint-icon {
      font-size: 3rem;
      color: #0d6efd;
      margin-bottom: 1rem;
    }
    .error-msg {
      color: #ef4444;
      font-size: 0.875rem;
      margin-top: 0.5rem;
      display: none;
    }
    .text-info {
        color: #6ea8fe !important;
    }
  </style>
</head>

<body>

  <div class="login-card p-4 text-center">
    <div class="fingerprint-icon mb-2">
      <i class="bi bi-fingerprint" style="font-size: 2.5rem;"></i>
    </div>
    <h4 class="mb-1">Welcome Back</h4>
    <p class="text-secondary mb-3 small">Prisoner Attendance System</p>

    <form id="loginForm" onsubmit="handleLogin(event)">
      <div class="mb-3 text-start">
        <label class="form-label small text-secondary mb-1">User ID</label>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-transparent border-secondary border-opacity-25 text-secondary">
            <i class="bi bi-person"></i>
          </span>
          <input type="text" name="user_id" id="userId" class="form-control border-start-0 ps-3" placeholder="Enter ID" required>
        </div>
      </div>

      <div class="mb-3 text-start">
        <label class="form-label small text-secondary mb-1">Password</label>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-transparent border-secondary border-opacity-25 text-secondary">
            <i class="bi bi-lock"></i>
          </span>
          <input type="password" name="password" id="password" class="form-control border-start-0 ps-3" placeholder="Enter Password" required>
        </div>
      </div>

      <div id="errorMsg" class="error-msg mb-2">
        <i class="bi bi-exclamation-circle me-1"></i> <span id="errorText">Invalid User ID or Password</span>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-sm py-2">
          Login
        </button>
      </div>
    </form>

    <div class="mt-3 pt-2 border-top border-secondary border-opacity-25">
      <small class="text-secondary" style="font-size: 0.8rem;">
        Forgot credentials? <a href="contact-admin.html" class="text-info text-decoration-none">Contact Admin</a>
      </small>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function handleLogin(event) {
      event.preventDefault();
      
      const form = document.getElementById('loginForm');
      const formData = new FormData(form);
      const errorMsg = document.getElementById('errorMsg');
      const errorText = document.getElementById('errorText');
      
      fetch('../backend/login.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          errorMsg.style.display = 'none';
          // Open dashboard in new tab as requested
          window.open(data.redirect, '_blank');
          // Optional: redirect current page to a success msg or keep as is
        } else {
          errorText.innerText = data.message;
          errorMsg.style.display = 'block';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        errorText.innerText = 'Server error occurred.';
        errorMsg.style.display = 'block';
      });
    }

</script>
</body>
</html>