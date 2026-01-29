<?php
require_once '../backend/session_start.php';
include '../backend/db_connect.php';

if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch staff names (guards and wardens) for the activity feed
$staffNames = [];
$res = $conn->query("SELECT full_name FROM users WHERE role IN ('guard', 'warden') ORDER BY full_name");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $staffNames[] = $row['full_name'];
    }
} else {
    // Fallback if no staff found
    $staffNames = ["Duty Officer", "System Admin", "Internal Security"];
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
  <link rel="stylesheet" href="admin-style.css">
  
  <style>
    :root {
      --accent-color: #0d6efd;
    }
    
    /* Stats Metrics (Page Specific) */
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
  <?php include 'admin-sidebar.php'; ?>

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
          
          <div class="table-container" style="min-height: 480px;">
            <table class="table mb-0">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Time</th>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Details</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="activityFeed">
                <!-- Data will be populated by JavaScript -->
              </tbody>
            </table>
          </div>
          <div class="mt-3 d-flex justify-content-between align-items-center">
            <button id="prevPageBtn" class="btn btn-link text-primary text-decoration-none fw-600 p-0" style="font-size: 0.8rem;" disabled>
              <i class="bi bi-chevron-left"></i> Previous Page
            </button>
            <button id="nextPageBtn" class="btn btn-link text-primary text-decoration-none fw-600 p-0" style="font-size: 0.8rem;">
              Next Page <i class="bi bi-chevron-right"></i>
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
        const modalEl = document.getElementById('exportModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        const btn = event.currentTarget;
        const originalHtml = btn.innerHTML;
        
        try {
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2 pulse"></i> Generating...';
            btn.disabled = true;

            const response = await fetch(`../backend/get_export_data.php?type=${type}`);
            if (!response.ok) throw new Error('Network response was not ok');
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

            const columns = type === 'staff' 
                ? [["ID", "Name", "Role", "Designation", "Status", "Wing", "Shift"]]
                : [["ID", "Full Name", "Wing", "Cell", "Crime", "Admission", "Release"]];
            
            const rows = data.map(item => type === 'staff'
                ? [item.user_id, item.full_name, item.role, item.designation, item.status, item.assigned_wing, item.shift_type]
                : [item.prisoner_id, item.full_name, item.block_wing, item.cell_number, item.crime, item.admission_date, item.expected_release]
            );

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

            doc.save(`${type.charAt(0).toUpperCase() + type.slice(1)}_Report_${now.getTime()}.pdf`);
            
            // Reliable Modal Hiding
            if (modal) modal.hide();
            cleanupBackdrops();

        } catch (error) {
            console.error('Export Error:', error);
            alert('Failed to generate report. Please try again.');
        } finally {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    }

    function cleanupBackdrops() {
      document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
      document.body.classList.remove('modal-open');
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
    }

    document.addEventListener('DOMContentLoaded', () => {
      const sidebar = document.querySelector('.sidebar');
      const toggler = document.querySelector('.sidebar-toggler');
      
      if(toggler) {
        toggler.addEventListener('click', () => {
          sidebar.classList.toggle('show');
        });
      }

      document.addEventListener('click', (e) => {
        if (window.innerWidth <= 992) {
          if (sidebar && !sidebar.contains(e.target) && toggler && !toggler.contains(e.target) && sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
          }
        }
      });

      // --- Dynamic Persistent Activity Feed Logic ---
      const activityFeed = document.getElementById('activityFeed');
      const guardNames = <?php echo json_encode($staffNames); ?>;
      const activityTypes = ["Biometric Check", "Meal Attendance", "Wing Transfer", "Medical Visit", "Security Patrol", "Visitation Log", "Workshop Shift", "Recreation Period"];
      
      const statuses = [
        { label: 'Done', class: 'bg-success-soft' },
        { label: 'Live', class: 'bg-warning-soft' },
        { label: 'Missed', class: 'bg-danger-soft' }
      ];

      const ITEMS_PER_PAGE = 8;
      let currentPage = 0;
      
      // Load and Validate Activities (clear if old structure)
      let activities = JSON.parse(localStorage.getItem('prison_activities_v2') || '[]');
      if (activities.length > 0 && !activities[0].date) {
        activities = []; // Migrate
      }

      function createActivity(isVeryOld = false) {
        const now = new Date();
        if (isVeryOld) {
          now.setDate(now.getDate() - Math.floor(Math.random() * 10));
          now.setMinutes(now.getMinutes() - Math.floor(Math.random() * 1000));
        }
        
        const dateOptions = { month: 'short', day: 'numeric' };
        return {
          date: now.toLocaleDateString('en-US', dateOptions),
          time: now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
          id: '#LOG-' + Math.floor(1000 + Math.random() * 9000),
          name: guardNames[Math.floor(Math.random() * guardNames.length)],
          details: activityTypes[Math.floor(Math.random() * activityTypes.length)],
          status: statuses[Math.floor(Math.random() * statuses.length)],
          timestamp: now.getTime()
        };
      }

      function updateDisplay() {
        activityFeed.innerHTML = '';
        const start = currentPage * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;
        const pageItems = activities.slice(start, end);

        pageItems.forEach((act, index) => {
          const row = document.createElement('tr');
          row.style.opacity = '0';
          row.style.transform = 'translateY(-5px)';
          row.style.transition = `all 0.3s ease ${index * 0.05}s`;
          
          row.innerHTML = `
            <td style="font-size: 0.75rem;">${act.date}</td>
            <td style="font-size: 0.75rem;">${act.time}</td>
            <td style="font-size: 0.75rem;"><span class="text-primary">${act.id}</span></td>
            <td style="font-size: 0.75rem; white-space: nowrap;">${act.name}</td>
            <td style="font-size: 0.75rem; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${act.details}</td>
            <td><span class="badge-custom ${act.status.class}" style="font-size: 0.6rem;">${act.status.label}</span></td>
          `;
          activityFeed.appendChild(row);
          setTimeout(() => {
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
          }, 10);
        });

        // Update nav buttons
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');
        if(prevBtn) prevBtn.disabled = currentPage === 0;
        if(nextBtn) nextBtn.disabled = end >= activities.length;
      }

      // Initialize activity history if empty
      if (activities.length === 0) {
        for (let i = 0; i < 24; i++) {
          activities.push(createActivity(true));
        }
        activities.sort((a,b) => b.timestamp - a.timestamp);
      }

      // Add exactly ONE new activity per login/load
      const newActivity = createActivity();
      activities.unshift(newActivity);
      
      // Keep only last 100 items
      if (activities.length > 100) activities = activities.slice(0, 100);
      
      localStorage.setItem('prison_activities_v2', JSON.stringify(activities));

      // Navigation Logic
      document.getElementById('nextPageBtn')?.addEventListener('click', () => {
        if ((currentPage + 1) * ITEMS_PER_PAGE < activities.length) {
          currentPage++;
          updateDisplay();
        }
      });
 
       document.getElementById('prevPageBtn')?.addEventListener('click', () => {
         if (currentPage > 0) {
           currentPage--;
           updateDisplay();
         }
       });
 
       // Initial Render
       updateDisplay();
     });
   </script>
 </body>
 </html>
