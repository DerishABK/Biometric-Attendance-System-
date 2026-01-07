<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Prisoner Registration – Help & Guidelines</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    body {
      min-height: 100vh;
      background: radial-gradient(circle at top left, #111827, #010101 70%);
      color: #f3f4f6;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      padding: 40px 20px;
    }
    
    .guide-container {
      max-width: 900px;
      margin: 0 auto;
    }

    .app-card {
      border-radius: 1.2rem;
      border: 1px solid rgba(255, 255, 255, 0.08);
      background: rgba(17, 24, 39, 0.7);
      backdrop-filter: blur(15px);
      padding: 2.5rem;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    h1, h4 {
      color: #60a5fa;
      font-weight: 700;
    }

    .guideline-item {
      display: flex;
      gap: 1.5rem;
      margin-bottom: 2rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .guideline-item:last-child {
      border-bottom: none;
    }

    .icon-box {
      width: 48px;
      height: 48px;
      background: rgba(37, 99, 235, 0.1);
      border: 1px solid rgba(37, 99, 235, 0.2);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #3b82f6;
      flex-shrink: 0;
    }

    .alert-box {
      background: rgba(245, 158, 11, 0.1);
      border: 1px solid rgba(245, 158, 11, 0.2);
      color: #fbbf24;
      padding: 1.25rem;
      border-radius: 0.75rem;
      margin-top: 2rem;
    }

    .btn-outline-light {
      border-color: rgba(255, 255, 255, 0.2);
    }
  </style>
</head>
<body>

  <div class="guide-container">
    
    <div class="d-flex justify-content-between align-items-center mb-5">
      <div>
        <h1 class="mb-2">Registration Guidelines</h1>
        <p class="text-secondary">Official protocols for Warden and Prison Staff</p>
      </div>
      <button onclick="window.close()" class="btn btn-outline-light btn-sm">
        <i class="bi bi-x-lg me-1"></i> Close Tab
      </button>
    </div>

    <div class="app-card">
      
      <div class="guideline-item">
        <div class="icon-box">
          <i class="bi bi-shield-check fs-4"></i>
        </div>
        <div>
          <h4>1. Verify Court Documents</h4>
          <p class="text-secondary">Before entering any data, ensure that the prisoner's commitment order and identification papers are verified by the legal department.</p>
        </div>
      </div>

      <div class="guideline-item">
        <div class="icon-box">
          <i class="bi bi-person-bounding-box fs-4"></i>
        </div>
        <div>
          <h4>2. Accuracy of Personal Data</h4>
          <p class="text-secondary">Full name must match the legal identification exactly. Date of birth is critical for determining age-specific housing protocols.</p>
        </div>
      </div>

      <div class="guideline-item">
        <div class="icon-box">
          <i class="bi bi-fingerprint fs-4"></i>
        </div>
        <div>
          <h4>3. Biometric Protocol</h4>
          <p class="text-secondary">Ensure the fingerprint sensor is clean. The system requires at least two high-quality scans of the right index finger for successful enrollment.</p>
        </div>
      </div>

      <div class="guideline-item">
        <div class="icon-box">
          <i class="bi bi-hospital fs-4"></i>
        </div>
        <div>
          <h4>4. Health Screening</h4>
          <p class="text-secondary">An initial medical review must be completed. Note any visible injuries or chronic conditions in the permanent address/notes section if applicable.</p>
        </div>
      </div>

      <div class="alert-box">
        <div class="d-flex gap-3">
          <i class="bi bi-exclamation-triangle-fill fs-4"></i>
          <div>
            <strong>Important Security Alert:</strong>
            <p class="mb-0 mt-1 small">Once registration is complete, the prisoner ID is locked to the biometric profile. Double-check all cell assignments (Block/Wing) before clicking 'Complete Registration'.</p>
          </div>
        </div>
      </div>

      <div class="mt-5 pt-4 text-center border-top border-secondary border-opacity-25">
        <p class="small text-secondary mb-0">For technical assistance, contact the Security Systems Admin immediately.</p>
      </div>

    </div>

  </div>

</body>
</html>
