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
  <title>Leave Management - Prison Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="admin-style.css">
  <style>
    /* Table Styling (Matching other admin pages) */
    .table { width: 100%; border-collapse: collapse; color: #f8fafc !important; }
    .table thead th { 
      background: rgba(255, 255, 255, 0.03);
      border-bottom: 2px solid rgba(255, 255, 255, 0.1); 
      color: rgba(255, 255, 255, 0.6); 
      font-size: 0.75rem; 
      text-transform: uppercase;
      padding: 0.75rem;
    }
    .table tbody td { 
      vertical-align: middle; 
      border-bottom: 1px solid rgba(255, 255, 255, 0.03); 
      padding: 0.75rem;
    }
    .table tr:hover { background: rgba(255, 255, 255, 0.02); }

    .status-badge { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
    .status-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
    .status-approved { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
    .status-rejected { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
    
    .text-secondary { color: rgba(255, 255, 255, 0.6) !important; }
  </style>
</head>
<body>
  <?php include 'admin-sidebar.php'; ?>

  <div class="main-content">
    <div class="mb-4">
      <h1 class="h3 fw-bold">Leave Management</h1>
      <p class="text-secondary">Review and respond to staff leave applications</p>
    </div>

    <div class="app-card">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Staff Name</th>
              <th>Date</th>
              <th>Shift</th>
              <th>Duration</th>
              <th>Alternative Staff</th>
              <th>Reason</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="leaveTableBody">
            <tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Loading applications...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function loadLeaves() {
      fetch('../backend/get_leave_applications.php')
        .then(r => r.json())
        .then(data => {
          if (data.status === 'success') {
            const tbody = document.getElementById('leaveTableBody');
            tbody.innerHTML = '';
            if (data.data.length === 0) {
              tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">No leave applications found.</td></tr>';
              return;
            }
            data.data.forEach(leave => {
              const statusClass = 'status-' + leave.status.toLowerCase();
              let actions = '';
              if (leave.status === 'Pending') {
                actions = `
                  <div class="btn-group btn-group-sm">
                    <button class="btn btn-success" onclick="manageLeave(${leave.id}, 'Approved', this)">Approve</button>
                    <button class="btn btn-danger" onclick="manageLeave(${leave.id}, 'Rejected', this)">Reject</button>
                  </div>
                `;
              } else {
                actions = '<span class="text-muted small">Processed</span>';
              }
              
              const row = `
                <tr>
                  <td>
                    <div class="fw-bold">${leave.full_name}</div>
                    <div class="small text-secondary">${leave.designation}</div>
                  </td>
                  <td>${new Date(leave.leave_date).toLocaleDateString()}</td>
                  <td>${leave.shift}</td>
                  <td>${leave.duration}</td>
                  <td class="small">${leave.alt_staff_name || 'N/A'}</td>
                  <td><small>${leave.reason}</small></td>
                  <td><span class="status-badge ${statusClass}">${leave.status}</span></td>
                  <td>${actions}</td>
                </tr>
              `;
              tbody.insertAdjacentHTML('beforeend', row);
            });
          }
        });
    }

    function manageLeave(id, action, btn) {
      const originalContent = btn.parentElement.innerHTML;
      btn.parentElement.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
      
      const formData = new FormData();
      formData.append('leave_id', id);
      formData.append('action', action);

      fetch('../backend/manage_leave.php', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        if (data.status === 'success') {
          loadLeaves();
        } else {
          alert('Error: ' + data.message);
          btn.parentElement.innerHTML = originalContent;
        }
      })
      .catch(e => {
        console.error(e);
        alert('An error occurred.');
        btn.parentElement.innerHTML = originalContent;
      });
    }

    document.addEventListener('DOMContentLoaded', loadLeaves);
  </script>
</body>
</html>
