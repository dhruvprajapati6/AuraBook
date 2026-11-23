<?php
session_start();
// Redirect path capture karein (jaise user sell.php se aaya ho)
$redirect = isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : '';

$village_filter = isset($_GET['village']) ? trim($_GET['village']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Register - AuraBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .auth-card {
            max-width: 450px;
            margin: 50px auto;
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background: #ffffff;
            overflow: hidden;
        }
        .nav-pills .nav-link {
            color: #198754;
            font-weight: 700;
            border-radius: 0;
            padding: 14px;
        }
        .nav-pills .nav-link.active {
            background-color: #198754;
            color: #ffffff;
        }
        .btn-success-custom {
            background-color: #198754;
            color: white;
            font-weight: 700;
            padding: 10px;
            border-radius: 8px;
        }
        .btn-success-custom:hover {
            background-color: #146c43;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="auth-card">
        
        <!-- Tab Navigation Header -->
        <ul class="nav nav-pills nav-justified" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>Login
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register" type="button" role="tab">
                    <i class="fa-solid fa-user-plus me-2"></i>Register
                </button>
            </li>
        </ul>

        <div class="p-4">
            <!-- Alert Messages -->
            <?php if (isset($_GET['error'])): ?>
                <?php if ($_GET['error'] == 'invalid_credentials'): ?>
                    <div class="alert alert-danger py-2 small mb-3">❌ Galt Email ya Password!</div>
                <?php elseif ($_GET['error'] == 'email_exists'): ?>
                    <div class="alert alert-warning py-2 small mb-3">⚠️ Yeh Email pehle se registered hai!</div>
                <?php elseif ($_GET['error'] == 'reg_failed'): ?>
                    <div class="alert alert-danger py-2 small mb-3">❌ Registration nahi ho paya. Dobara try karein.</div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="tab-content" id="pills-tabContent">
                
                <!-- 1. LOGIN FORM -->
                <div class="tab-pane fade show active" id="pills-login" role="tabpanel">
                    
                    <form action="auth.php" method="POST">
                        <input type="hidden" name="redirect" value="<?= $redirect ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" name="login" class="btn btn-success-custom w-100 mb-2">
                            Login Karein <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    </form>
                </div>
               

<!-- FORGET PASSWORD LINK -->
            <div class="text-end mb-4" >
                <a href="#login" class="text-decoration-none small fw-bold text-success" data-bs-toggle="modal" data-bs-target="#forgetPasswordModal">
                    Forgot Password?
                </a>
            </div>
        </form>

 

                <!-- 2. REGISTRATION FORM -->
                <div class="tab-pane fade" id="pills-register" role="tabpanel">
                    <form action="auth.php" method="POST">
                        <input type="hidden" name="redirect" value="<?= $redirect ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">Poora Naam (Full Name)</label>
                            <input type="text" name="name" class="form-control" placeholder="Mr. Dhruv Prajapati" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="dhruv23@gmail.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">Mobile Number (Phone)</label>
                            <input type="tel" name="phone" class="form-control" placeholder="6352870496" pattern="[0-9]{10}" required>
                        </div>

                        <div class="mb-3">
                            
                            <select name="village_name" class="form-select" required>
                                   <option value="">Select Villege</option>
                                <?php 
                    $villages = ['Karnala', 'Moteta', 'Khodla', 'Motahada', 'Mumanvas', 'Vasan', 'Gola', 'Jalotra', 'Talepura', 'Pimpli'];
                    foreach($villages as $v):
                    ?>
                        <option value="<?= $v ?>" <?= $village_filter == $v ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">Password Banayein</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" minlength="6" required>
                        </div>

                        <button type="submit" name="register" class="btn btn-success-custom w-100">
                            Register Karein <i class="fa-solid fa-user-check ms-1"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
        
        <div class="card-footer text-center bg-light py-3 border-0">
            <a href="index.php" class="text-decoration-none text-muted small fw-bold">
                <i class="fa-solid fa-house me-1"></i> Home Par Wapas Jayein
            </a>
        </div>
    </div>
</div>



<!-- ========================================== -->
<!-- FORGET PASSWORD MODAL (POPUP) -->
<!-- ========================================== -->
<div class="modal fade" id="forgetPasswordModal" tabindex="-1" aria-labelledby="forgetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            
            <div class="modal-header bg-success text-white border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold" id="forgetModalLabel">
                    <i class="fa-solid fa-key me-2"></i>Reset Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <p class="text-muted small mb-3">
                    Aapna Registered **Mobile Number** aur **Email** verify karke new password set karein.
                </p>

                <form action="auth.php" method="POST">
                    
                    <!-- 1. Registered Phone -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Registered Phone Number *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-phone"></i></span>
                            <input type="tel" name="forget_phone" class="form-control border-start-0" placeholder="10 Digit Mobile" pattern="[0-9]{10}" required>
                        </div>
                    </div>

                    <!-- 2. Registered Email -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Registered Email Address *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" name="forget_email" class="form-control border-start-0" placeholder="yourname@gmail.com" required>
                        </div>
                    </div>

                    <!-- 3. New Password -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">New Password *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="new_password" class="form-control border-start-0" minlength="6" placeholder="Enter new password" required>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-3 fw-bold w-50" data-bs-dismiss="modal">❎ Close</button>
                        <button type="submit" name="reset_password_btn" class="btn btn-success rounded-3 fw-bold w-50 py-2">
                            Update Password
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>