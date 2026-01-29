<?php
// admin-sidebar.php - Standardized Sidebar for Admin Panel
$current_page = basename($_SERVER['PHP_SELF']);
?>
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
        <a href="dashboard-admin.php" class="nav-link <?php echo ($current_page == 'dashboard-admin.php') ? 'active' : ''; ?>">
          <i class="bi bi-grid-1x2-fill"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="admin-prisoner-list.php" class="nav-link <?php echo ($current_page == 'admin-prisoner-list.php') ? 'active' : ''; ?>">
          <i class="bi bi-people-fill"></i>
          <span>Prisoners</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="staff-list.php" class="nav-link <?php echo ($current_page == 'staff-list.php') ? 'active' : ''; ?>">
          <i class="bi bi-person-badge-fill"></i>
          <span>Staff</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="admin-visitation.php" class="nav-link <?php echo ($current_page == 'admin-visitation.php') ? 'active' : ''; ?>">
          <i class="bi bi-calendar-event-fill"></i>
          <span>Visitation</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="admin-leave-management.php" class="nav-link <?php echo ($current_page == 'admin-leave-management.php') ? 'active' : ''; ?>">
          <i class="bi bi-calendar-range-fill"></i>
          <span>Leave Requests</span>
          <span id="leaveBadge" class="badge rounded-pill bg-danger ms-auto d-none">0</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="admin-settings.php" class="nav-link <?php echo ($current_page == 'admin-settings.php') ? 'active' : ''; ?>">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Sidebar Toggler Logic (for mobile)
    const togglers = document.querySelectorAll('.sidebar-toggler');
    const sidebar = document.querySelector('.sidebar');
    
    togglers.forEach(toggler => {
        toggler.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 992 && sidebar && sidebar.classList.contains('show')) {
            let isClickInsideSidebar = sidebar.contains(e.target);
            let isClickOnToggler = false;
            togglers.forEach(t => { if(t.contains(e.target)) isClickOnToggler = true; });
            
            if (!isClickInsideSidebar && !isClickOnToggler) {
                sidebar.classList.remove('show');
            }
        }
    });

    // 2. Fetch Leave Notification Count
    function fetchLeaveCount() {
        fetch('../backend/get_pending_leaves_count.php')
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success' && data.count > 0) {
                    const badge = document.getElementById('leaveBadge');
                    if (badge) {
                        badge.innerText = data.count;
                        badge.classList.remove('d-none');
                    }
                } else if (data.status === 'success' && data.count === 0) {
                    const badge = document.getElementById('leaveBadge');
                    if (badge) badge.classList.add('d-none');
                }
            })
            .catch(e => console.error('Error fetching leave count:', e));
    }
    fetchLeaveCount();
    setInterval(fetchLeaveCount, 30000); // Update every 30s
});
</script>
