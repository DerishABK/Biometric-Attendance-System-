<?php
require_once '../backend/session_start.php';
if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'warden') {
    header("Location: index.php");
    exit();
}
?>
<html lang="en"><head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Warden Dashboard – Prisoner Registration</title>

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
    
    /* Navbar styling */
    .navbar {
      background: rgba(15, 23, 42, 0.95);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .navbar-brand {
      color: #f8f9fa !important;
      font-weight: 600;
    }
    .nav-link {
      color: rgba(255, 255, 255, 0.7) !important;
    }
    .nav-link:hover, .nav-link.active {
      color: #0d6efd !important;
    }

    /* Card styling */
    .app-card {
      border-radius: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background: rgba(15, 23, 42, 0.8);
      backdrop-filter: blur(12px);
    }
    
    /* Form controls */
    .form-control, .form-select {
      background-color: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: #f8f9fa;
      border-radius: 0.5rem;
    }
    .form-control:focus, .form-select:focus {
      background-color: rgba(255, 255, 255, 0.1);
      border-color: #0d6efd;
      color: #fff;
      box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.3);
    }
    
    /* Ensure select options are visible */
    select option {
      background-color: #0f172a;
      color: #f8f9fa;
    }
    
    /* Inline Validation Styles */
    .invalid-feedback {
      font-size: 0.75rem;
      color: #fb7185; /* Soft red for dark theme */
      margin-top: 0.25rem;
      font-weight: 500;
    }
    .is-invalid {
      border-color: #fb7185 !important;
      background-color: rgba(251, 113, 133, 0.05) !important;
    }
    .is-invalid:focus {
      box-shadow: 0 0 0 0.25rem rgba(251, 113, 133, 0.25) !important;
    }
    
    /* Custom file upload */
    .upload-box {
      border: 2px dashed rgba(255, 255, 255, 0.2);
      border-radius: 0.75rem;
      height: 200px; /* Set fixed height to prevent oversizing */
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      transition: all 0.2s;
      cursor: pointer;
      background: rgba(255, 255, 255, 0.02);
      overflow: hidden; /* Prevent image from overlapping or spilling out */
      position: relative;
    }
    .upload-box:hover {
      border-color: #0d6efd;
      background: rgba(13, 110, 253, 0.05);
    }

    .btn-primary {
      background-color: #0d6efd;
      border-color: #0d6efd;
      color: #fff;
      font-weight: 600;
    }
    .btn-primary:hover {
      background-color: #0b5ed7;
      border-color: #0b5ed7;
    }
    
    .section-title {
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding-bottom: 0.5rem;
      margin-bottom: 1.5rem;
      color: #0d6efd;
      font-size: 1.1rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    /* Step Wizard */
    .step-indicator {
      display: flex;
      justify-content: space-between;
      margin-bottom: 2rem;
      position: relative;
    }
    .step-indicator::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 2px;
      background: rgba(255, 255, 255, 0.1);
      z-index: 0;
      transform: translateY(-50%);
    }
    .step {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #0f172a;
      border: 2px solid rgba(255, 255, 255, 0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      color: rgba(255, 255, 255, 0.5);
      position: relative;
      z-index: 1;
      transition: all 0.3s ease;
    }
    .step.active {
      border-color: #0d6efd;
      background: #0d6efd;
      color: #fff;
      box-shadow: 0 0 15px rgba(13, 110, 253, 0.4);
    }
    .step.completed {
      border-color: #0d6efd;
      background: #0f172a;
      color: #0d6efd;
    }
    .step-label {
      position: absolute;
      top: 50px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 0.75rem;
      white-space: nowrap;
      color: rgba(255, 255, 255, 0.5);
    }
    .step.active .step-label {
      color: #0d6efd;
      font-weight: 600;
    }

    .form-step {
      display: none;
      animation: fadeIn 0.4s ease;
    }
    .form-step.active {
      display: block;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .text-info {
        color: #6ea8fe !important;
    }
    .badge.bg-info {
        background-color: #6ea8fe !important;
    }
    .btn-outline-info {
        --bs-btn-color: #6ea8fe;
        --bs-btn-border-color: #6ea8fe;
        --bs-btn-hover-color: #000;
        --bs-btn-hover-bg: #6ea8fe;
        --bs-btn-hover-border-color: #6ea8fe;
        --bs-btn-focus-shadow-rgb: 110, 168, 254;
        --bs-btn-active-color: #000;
        --bs-btn-active-bg: #6ea8fe;
        --bs-btn-active-border-color: #6ea8fe;
    }

    /* Camera Overlay Styles */
    .camera-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 10;
    }
    .face-frame {
      width: 260px;
      height: 330px;
      border: 3px solid #ef4444; /* Default Red */
      border-radius: 50% / 40%;
      box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.6);
      transition: all 0.3s ease;
      position: relative;
    }
    .face-frame::after {
      content: 'POSITION FACE IN FRAME';
      position: absolute;
      top: -35px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 0.7rem;
      font-weight: bold;
      color: white;
      white-space: nowrap;
      letter-spacing: 1px;
      background: rgba(239, 68, 68, 0.8);
      padding: 2px 10px;
      border-radius: 4px;
    }
    .face-frame.status-ok {
      border-color: #10b981; /* Green */
      box-shadow: 0 0 20px rgba(16, 185, 129, 0.4), 0 0 0 1000px rgba(0, 0, 0, 0.4);
    }
    .face-frame.status-ok::after {
      content: 'FACE DETECTED';
      background: rgba(16, 185, 129, 0.8);
    }
    .camera-instructions {
      position: absolute;
      bottom: 80px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.9);
      text-align: center;
      width: 100%;
      text-shadow: 0 2px 4px rgba(0,0,0,0.8);
      font-weight: 500;
    }
    .face-frame {
      border-color: #10b981 !important; /* Always Green */
      box-shadow: 0 0 15px rgba(16, 185, 129, 0.4), 0 0 0 1000px rgba(0, 0, 0, 0.5) !important;
    }
    .face-frame::after {
      content: 'ALIGN FACE IN GRID' !important;
      background: rgba(16, 185, 129, 0.8) !important;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">
        <i class="bi bi-building-lock me-2"></i>Warden Dashboard
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" href="#"><i class="bi bi-person-plus me-1"></i> Registration</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="prisoner-list.php" target="_blank"><i class="bi bi-list-ul me-1"></i> Prisoner List</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="help-guidelines.php" target="_blank"><i class="bi bi-question-circle me-1"></i> Help</a>
          </li>
          <li class="nav-item ms-lg-3 dropdown">
            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; color: white;">
                <i class="bi bi-person-fill"></i>
              </div>
              <span>Profile</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow-lg" aria-labelledby="profileDropdown">
              <li><a class="dropdown-item" href="dashboard-warden.php"><i class="bi bi-speedometer2 me-2"></i> Main Page</a></li>
              <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-circle me-2"></i> Profile</a></li>
              <li><hr class="dropdown-divider border-secondary"></li>
              <li><a class="dropdown-item text-danger fw-bold" href="../backend/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container" style="margin-top: 100px; margin-bottom: 40px;">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2><i class="bi bi-person-plus-fill me-2"></i>New Prisoner Registration</h2>
          <span class="badge bg-info text-dark">Warden Access Only</span>
        </div>

        <!-- Step Indicator -->
        <div class="step-indicator px-5">
          <div class="step active" id="step-indicator-1">1
            <span class="step-label">Personal</span>
          </div>
          <div class="step" id="step-indicator-2">2
            <span class="step-label">Incarceration</span>
          </div>
          <div class="step" id="step-indicator-3">3
            <span class="step-label">Biometric</span>
          </div>
        </div>

        <form class="app-card p-4 p-md-5 shadow-lg" id="registrationForm" onsubmit="handleSubmit(event)">
          
          <!-- Step 1: Personal Details -->
          <div class="form-step active" id="step-1">
            <h5 class="section-title"><i class="bi bi-person-vcard me-2"></i>Personal Details</h5>
            <div class="row g-3 mb-4">
              <div class="col-md-12">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" required="" placeholder="e.g. John Doe">
                <div class="invalid-feedback">Please enter a valid name (alphabets only).</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="dob" class="form-control" required="">
                <div class="invalid-feedback">Date of birth is required.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select" required="">
                  <option value="" selected="" disabled="">Select Gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                </select>
                <div class="invalid-feedback">Please select a gender.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Nationality</label>
                <input type="text" name="nationality" class="form-control" required="" placeholder="e.g. Indian">
                <div class="invalid-feedback">Please enter a valid nationality.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Contact Number (Emergency)</label>
                <input type="tel" name="contact_number" class="form-control" required="" placeholder="+91 98765 43210">
                <div class="invalid-feedback">Please enter a valid contact number.</div>
              </div>
              <div class="col-12">
                <label class="form-label">Permanent Address</label>
                <textarea name="address" class="form-control" rows="3" required="" placeholder="Enter full address"></textarea>
                <div class="invalid-feedback">Please enter a valid address (alphanumeric and common symbols only).</div>
              </div>
            </div>
            <div class="d-flex justify-content-end">
              <button type="button" class="btn btn-primary px-4" onclick="nextStep(2)">
                Next <i class="bi bi-arrow-right ms-2"></i>
              </button>
            </div>
          </div>

          <!-- Step 2: Incarceration Details -->
          <div class="form-step" id="step-2">
            <h5 class="section-title"><i class="bi bi-file-earmark-text me-2"></i>Incarceration Details</h5>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label">Prisoner ID (Auto-generated)</label>
                <input type="text" name="prisoner_id_display" class="form-control" value="P-2025-0042" readonly="" style="background: rgba(255,255,255,0.05); cursor: not-allowed;">
              </div>
              <div class="col-md-6">
                <label class="form-label">Block / Wing</label>
                <select name="block_wing" class="form-select" required="">
                  <option value="" selected="" disabled="">Select Block</option>
                  <option value="Main Block A">Main Block A</option>
                  <option value="Main Block B">Main Block B</option>
                  <option value="High Security">High Security</option>
                  <option value="Women's Wing">Women's Wing</option>
                </select>
                <div class="invalid-feedback">Please select a block/wing.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Cell Number</label>
                <input type="text" name="cell_number" class="form-control" required="" placeholder="e.g. C-104">
                <div class="invalid-feedback">Please enter a valid cell number (alphanumeric).</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Crime / Offense</label>
                <input type="text" name="crime" class="form-control" required="" placeholder="e.g. Burglary">
                <div class="invalid-feedback">Please enter a valid crime description (alphabets only).</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Sentence Duration</label>
                <input type="text" name="sentence_duration" class="form-control" required="" placeholder="e.g. 5 Years">
                <div class="invalid-feedback">Format must be 'X Years' (e.g., 5 Years).</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Admission Date</label>
                <input type="date" name="admission_date" class="form-control" readonly style="background: rgba(255,255,255,0.05); cursor: not-allowed;">
                <div class="invalid-feedback">Admission date is required.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Expected Release</label>
                <input type="date" name="expected_release" class="form-control" readonly style="background: rgba(255,255,255,0.05); cursor: not-allowed;">
              </div>
            </div>
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-outline-light px-4" onclick="prevStep(1)">
                <i class="bi bi-arrow-left me-2"></i> Previous
              </button>
              <button type="button" class="btn btn-primary px-4" onclick="nextStep(3)">
                Next <i class="bi bi-arrow-right ms-2"></i>
              </button>
            </div>
          </div>

          <!-- Step 3: Biometric & Identification -->
          <div class="form-step" id="step-3">
            <h5 class="section-title"><i class="bi bi-fingerprint me-2"></i>Biometric &amp; Identification</h5>
            <div class="row g-4 mb-4">
              
              <!-- Photo Section -->
              <div class="col-12">
                <label class="form-label mb-3">Biometric Enrollment (Face Recognition - 3 Angles)</label>
                <div class="row g-3">
                  <!-- Front Angle -->
                  <div class="col-md-4">
                    <div class="angle-label mb-2 small fw-bold text-center">FRONT VIEW</div>
                    <div class="upload-options d-flex gap-2 mb-2">
                      <button type="button" class="btn btn-sm btn-outline-primary flex-grow-1" onclick="openCamera('front')">
                        <i class="bi bi-camera me-1"></i> Capture
                      </button>
                    </div>
                    <div class="upload-box" id="photoPreview-front">
                      <i class="bi bi-person-bounding-box fs-1 text-secondary"></i>
                      <p class="mb-0 mt-2 text-secondary small">Front View</p>
                    </div>
                    <input type="hidden" id="capturedPhoto-front" name="photo_front">
                  </div>
                  
                  <!-- Left Angle -->
                  <div class="col-md-4">
                    <div class="angle-label mb-2 small fw-bold text-center">LEFT PROFILE</div>
                    <div class="upload-options d-flex gap-2 mb-2">
                      <button type="button" class="btn btn-sm btn-outline-primary flex-grow-1" onclick="openCamera('left')">
                        <i class="bi bi-camera me-1"></i> Capture
                      </button>
                    </div>
                    <div class="upload-box" id="photoPreview-left">
                      <i class="bi bi-person-bounding-box fs-1 text-secondary"></i>
                      <p class="mb-0 mt-2 text-secondary small">Left Profile</p>
                    </div>
                    <input type="hidden" id="capturedPhoto-left" name="photo_left">
                  </div>

                  <!-- Right Angle -->
                  <div class="col-md-4">
                    <div class="angle-label mb-2 small fw-bold text-center">RIGHT PROFILE</div>
                    <div class="upload-options d-flex gap-2 mb-2">
                      <button type="button" class="btn btn-sm btn-outline-primary flex-grow-1" onclick="openCamera('right')">
                        <i class="bi bi-camera me-1"></i> Capture
                      </button>
                    </div>
                    <div class="upload-box" id="photoPreview-right">
                      <i class="bi bi-person-bounding-box fs-1 text-secondary"></i>
                      <p class="mb-0 mt-2 text-secondary small">Right Profile</p>
                    </div>
                    <input type="hidden" id="capturedPhoto-right" name="photo_right">
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-between pt-3 border-top border-secondary border-opacity-25">
              <button type="button" class="btn btn-outline-light px-4" onclick="prevStep(2)">
                <i class="bi bi-arrow-left me-2"></i> Previous
              </button>
              <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-check-lg me-2"></i> Complete Registration
              </button>
            </div>
          </div>

        </form>

      </div>
    </div>
  </div>

  <!-- Camera Modal -->
  <div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-dark text-white border-secondary">
        <div class="modal-header border-secondary">
          <h5 class="modal-title"><i class="bi bi-camera me-2"></i>Capture Photo</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="closeCamera()"></button>
        </div>
        <div class="modal-body p-0 overflow-hidden position-relative" style="aspect-ratio: 4/3; background: #000;">
          <video id="video" autoplay playsinline class="w-100 h-100" style="object-fit: cover;"></video>
          <canvas id="canvas" class="d-none"></canvas>
          
          <!-- Camera Overlay -->
          <div class="camera-overlay">
            <div class="face-frame" id="faceFrame"></div>
            <div class="camera-instructions" id="cameraInstructions">Align face within the grid for capture</div>
          </div>
          
          <div class="position-absolute bottom-0 start-50 translate-middle-x mb-3" style="z-index: 15;">
             <button type="button" id="captureBtn" class="btn btn-primary rounded-circle p-3 shadow-lg" onclick="takeSnapshot()" style="width: 60px; height: 60px;">
                <i class="bi bi-camera-fill fs-4"></i>
             </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    let stream = null;
    let currentAngle = 'front';
    const cameraModal = new bootstrap.Modal(document.getElementById('cameraModal'));
    
    async function openCamera(angle) {
      currentAngle = angle;
      const title = document.querySelector('#cameraModal .modal-title');
      title.innerHTML = `<i class="bi bi-camera me-2"></i>Capture ${angle.charAt(0).toUpperCase() + angle.slice(1)} View`;
      
      try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: 'user' 
            } 
        });
        const video = document.getElementById('video');
        video.srcObject = stream;
        cameraModal.show();
        
        // Always enable capture button in simple mode
        document.getElementById('captureBtn').disabled = false;
      } catch (err) {
        alert("Could not access camera: " + err.message);
      }
    }

    function closeCamera() {
      if (stream) {
        stream.getTracks().forEach(track => track.stop());
      }
    }

    function takeSnapshot() {
      const video = document.getElementById('video');
      const canvas = document.getElementById('canvas');
      const frame = document.getElementById('faceFrame');
      const context = canvas.getContext('2d');
      
      // Get dimensions for cropping
      const videoRect = video.getBoundingClientRect();
      const frameRect = frame.getBoundingClientRect();
      
      // Calculate scale between displayed video and actual stream pixels
      const scaleX = video.videoWidth / videoRect.width;
      const scaleY = video.videoHeight / videoRect.height;
      
      // Calculate crop area in source coordinates
      const startX = (frameRect.left - videoRect.left) * scaleX;
      const startY = (frameRect.top - videoRect.top) * scaleY;
      const width = frameRect.width * scaleX;
      const height = frameRect.height * scaleY;
      
      // Set canvas to a standard portrait resolution
      canvas.width = 480;
      canvas.height = 640;
      
      // Draw the cropped area to the canvas
      context.drawImage(video, startX, startY, width, height, 0, 0, canvas.width, canvas.height);
      
      const dataURL = canvas.toDataURL('image/jpeg', 0.9);
      document.getElementById(`capturedPhoto-${currentAngle}`).value = dataURL;
      
      // Update Preview
      const preview = document.getElementById(`photoPreview-${currentAngle}`);
      preview.innerHTML = `<img src="${dataURL}" class="w-100 h-100" style="object-fit: cover; position: absolute; top: 0; left: 0;">`;
      
      closeCamera();
      cameraModal.hide();
    }
    const totalSteps = 3;

    function showStep(step) {
      // Hide all steps
      document.querySelectorAll('.form-step').forEach(el => el.classList.remove('active'));
      // Show current step
      document.getElementById(`step-${step}`).classList.add('active');
      
      // Update indicators
      for (let i = 1; i <= totalSteps; i++) {
        const indicator = document.getElementById(`step-indicator-${i}`);
        if (i < step) {
          indicator.classList.add('completed');
          indicator.classList.remove('active');
          indicator.innerHTML = '<i class="bi bi-check"></i><span class="step-label">' + getLabel(i) + '</span>';
        } else if (i === step) {
          indicator.classList.add('active');
          indicator.classList.remove('completed');
          indicator.innerHTML = i + '<span class="step-label">' + getLabel(i) + '</span>';
        } else {
          indicator.classList.remove('active', 'completed');
          indicator.innerHTML = i + '<span class="step-label">' + getLabel(i) + '</span>';
        }
      }
      currentStep = step;
    }

    function getLabel(step) {
      if (step === 1) return 'Personal';
      if (step === 2) return 'Incarceration';
      if (step === 3) return 'Biometric';
      return '';
    }

    function validateInput(input, regex) {
      const value = input.value.trim();
      let isValid = true;
      
      if (input.required && !value) {
        isValid = false;
      } else if (value && regex && !regex.test(value)) {
        isValid = false;
      }

      // Special Logic: Date of Birth < Today
      if (input.name === 'dob' && value) {
        const selectedDate = new Date(value);
        const today = new Date();
        today.setHours(0,0,0,0);
        if (selectedDate >= today) {
          isValid = false;
          input.nextElementSibling.innerText = "Date of Birth must be in the past.";
        } else {
          input.nextElementSibling.innerText = "Date of birth is required.";
        }
      }
      
      if (isValid) {
        input.classList.remove('is-invalid');
      } else {
        input.classList.add('is-invalid');
      }
      return isValid;
    }

    function calculateReleaseDate() {
      const sentenceInput = document.querySelector('input[name="sentence_duration"]');
      const admissionInput = document.querySelector('input[name="admission_date"]');
      const releaseInput = document.querySelector('input[name="expected_release"]');
      
      const sentenceRegex = /^(\d+)\sYears$/i;
      const match = sentenceInput.value.trim().match(sentenceRegex);
      
      if (match && admissionInput.value) {
        const years = parseInt(match[1]);
        const admissionDate = new Date(admissionInput.value);
        admissionDate.setFullYear(admissionDate.getFullYear() + years);
        
        // Format to YYYY-MM-DD
        const releaseDateStr = admissionDate.toISOString().split('T')[0];
        releaseInput.value = releaseDateStr;
        sentenceInput.classList.remove('is-invalid');
      } else if (sentenceInput.value.trim() !== "") {
        sentenceInput.classList.add('is-invalid');
        releaseInput.value = "";
      }
    }

    function nextStep(step) {
      // Regex Patterns
      const nameRegex = /^[a-zA-Z\s]+$/;
      const contactRegex = /^((\+91[\-\s]?)?[0]?(91)?[6789]\d{9}|0\d{2,4}[\-\s]?\d{6,8})$/;
      const sentenceRegex = /^\d+\sYears$/i;
      const addressRegex = /^[a-zA-Z0-9\s,.\-\/#]+$/;
      const alphaNumRegex = /^[a-zA-Z0-9\-]+$/;
      
      let isStepValid = true;

      // Validate all inputs in the current active step
      const currentStepContainer = document.querySelector('.form-step.active');
      const inputs = currentStepContainer.querySelectorAll('input, select, textarea');
      
      inputs.forEach(input => {
        // Skip hidden or disabled inputs
        if (input.type === 'hidden' || input.disabled) return;

        // Find matching regex
        let regex = null;
        if (input.name === 'full_name' || input.name === 'nationality' || input.name === 'crime') regex = nameRegex;
        if (input.name === 'contact_number') regex = contactRegex;
        if (input.name === 'sentence_duration') regex = sentenceRegex;
        if (input.name === 'address') regex = addressRegex;
        if (input.name === 'cell_number') regex = alphaNumRegex;

        if (!validateInput(input, regex)) isStepValid = false;
      });

      if (!isStepValid) {
        // Find the first invalid element and focus it
        const firstInvalid = currentStepContainer.querySelector('.is-invalid');
        if (firstInvalid) firstInvalid.focus();
        return;
      }

      showStep(step);
    }

    // Add real-time validation listeners
    document.addEventListener('DOMContentLoaded', () => {
      const nameRegex = /^[a-zA-Z\s]+$/;
      const contactRegex = /^\+?[0-9\s\-]{7,15}$/;
      const sentenceRegex = /^\d+\sYears$/i;
      const addressRegex = /^[a-zA-Z0-9\s,.\-\/#]+$/;
      const alphaNumRegex = /^[a-zA-Z0-9\-]+$/;
 
      // Set System Date for Admission
      const admissionInput = document.querySelector('input[name="admission_date"]');
      const today = new Date().toISOString().split('T')[0];
      admissionInput.value = today;

      const fields = [
        { name: 'full_name', regex: nameRegex },
        { name: 'dob' },
        { name: 'gender' },
        { name: 'nationality', regex: nameRegex },
        { name: 'contact_number', regex: contactRegex },
        { name: 'address', regex: addressRegex },
        { name: 'block_wing' },
        { name: 'cell_number', regex: alphaNumRegex },
        { name: 'crime', regex: nameRegex },
        { name: 'sentence_duration', regex: sentenceRegex },
        { name: 'admission_date' }
      ];

      fields.forEach(field => {
        const el = document.querySelector(`[name="${field.name}"]`);
        if (el) {
          el.addEventListener('input', () => {
             validateInput(el, field.regex);
             if (field.name === 'sentence_duration') calculateReleaseDate();
          });
          el.addEventListener('blur', () => {
             validateInput(el, field.regex);
             if (field.name === 'sentence_duration') calculateReleaseDate();
          });
        }
      });
    });

    function prevStep(step) {
      showStep(step);
    }

    function handleSubmit(event) {
      event.preventDefault();
      
      const form = document.getElementById('registrationForm');
      const formData = new FormData(form);
      const btn = event.submitter;
      const originalText = btn.innerHTML;
      
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
      
      fetch('../backend/register_prisoner.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          alert(data.message + ' Prisoner ID: ' + data.prisoner_id);
          window.location.href = 'prisoner-list.php';
        } else {
          alert('Error: ' + data.message);
          btn.disabled = false;
          btn.innerHTML = originalText;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during registration.');
        btn.disabled = false;
        btn.innerHTML = originalText;
      });
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

    // Check for notifications on load
    document.addEventListener('DOMContentLoaded', () => {
      checkNotifications();
    });
  </script>


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

</body></html>