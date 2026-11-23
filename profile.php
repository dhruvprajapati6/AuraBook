<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- PROFILE IMAGE UPLOAD & CROP LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cropped_image'])) {
    $data = $_POST['cropped_image'];

    if (!empty($data)) {
        // Base64 Clean
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        // Upload Directory Setup
        $target_dir = 'uploads/profiles/';
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Unique Filename
        $filename = 'user_' . $user_id . '_' . time() . '.png';
        $filepath = $target_dir . $filename;

        if (file_put_contents($filepath, $data)) {
            // Database Update
            $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $stmt->execute([$filename, $user_id]);

            // Page Refresh for Update
            header("Location: profile.php?upload=success");
            exit();
        }
    }
}

// User Profile Fetch
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Profile Pic Set
$profile_pic = 'https://via.placeholder.com/150?text=No+Photo';
if (!empty($user['profile_image']) && file_exists('uploads/profiles/' . $user['profile_image'])) {
    $profile_pic = 'uploads/profiles/' . $user['profile_image'] . '?v=' . time();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meri Profile - AuraBook</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

    <style>
        body { background-color: #f4f7f6; font-family: sans-serif; }
        .profile-card { background: #ffffff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
        .profile-img-container {
            position: relative;
            width: 130px;
            height: 130px;
            margin: 0 auto;
        }
        .profile-img-container img {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #198754;
        }
        .change-photo-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #198754;
            color: #fff;
            border-radius: 50%;
            padding: 8px 10px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }
        .img-container-modal {
            max-height: 400px;
            text-align: center;
        }
        .img-container-modal img {
            max-width: 100%;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-success shadow-sm">
    <div class="container d-flex justify-content-between">
        <a class="navbar-brand fw-bold fs-4" href="index.php"><i class= "fa-solid fa-book"></i>&nbsp;&nbsp;AuraBook</a>
        <a href="logout.php" class="btn btn-sm btn-outline-light fw-bold"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a>
    </div>
</nav>

<div class="container my-5" style="max-width: 550px;">
    
    <?php if (isset($_GET['upload']) && $_GET['upload'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            🎉 Profile photo updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card profile-card p-4 border-0">
        
        <!-- PROFILE IMAGE SECTION -->
        <div class="text-center mb-4">
            <div class="profile-img-container mb-2">
                <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile Picture">
                <label for="imageInput" class="change-photo-btn" title="Change Photo">
                    <i class="fa-solid fa-camera"></i>
                </label>
                <input type="file" id="imageInput" accept="image/*" class="d-none">
            </div>
            
            <label for="imageInput" class="btn btn-sm btn-link text-success fw-bold text-decoration-none">
                <i class="fa-solid fa-pen-to-square me-1"></i> Change Photo
            </label>
        </div>

        <h5 class="fw-bold text-center text-dark mb-4 border-bottom pb-2">
            <i class="fa-solid fa-id-card text-success me-2"></i>Profile Details
        </h5>

        <div class="mb-3">
            <label class="form-label text-muted fw-bold small">Full Name</label>
            <p class="form-control-plaintext fs-6 fw-bold text-dark border-bottom px-2">
                <?= htmlspecialchars($user['name'] ?? $_SESSION['user_name'] ?? 'User') ?>
            </p>
        </div>

        <div class="mb-3">
            <label class="form-label text-muted fw-bold small">Mobile Number</label>
            <p class="form-control-plaintext fs-6 fw-bold text-dark border-bottom px-2">
                <?= htmlspecialchars($user['phone'] ?? $_SESSION['user_phone'] ?? 'N/A') ?>
            </p>
        </div>

        <div class="mb-4">
            <label class="form-label text-muted fw-bold small">Email Address</label>
            <p class="form-control-plaintext fs-6 fw-bold text-dark border-bottom px-2">
                <?= htmlspecialchars($user['email'] ?? $_SESSION['user_email'] ?? 'Not Added') ?>
            </p>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
            <a href="index.php" class="btn btn-light border fw-bold px-4 rounded-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
            <a href="logout.php" class="btn btn-danger fw-bold px-4 rounded-3">
                <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
            </a>
        </div>

    </div>

</div>

<!-- HIDDEN FORM FOR SUBMITTING CROPPED IMAGE -->
<form id="cropForm" method="POST" action="profile.php" style="display: none;">
    <input type="hidden" name="cropped_image" id="croppedImageInput">
</form>

<!-- CROPPER MODAL -->
<div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            
            <div class="modal-header bg-success text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-crop-simple me-2"></i>Crop Profile Photo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3">
                <div class="img-container-modal">
                    <img id="imageToCrop" src="" style="display: block; max-width: 100%;">
                </div>
            </div>

            <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary fw-bold rounded-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success fw-bold rounded-3 px-4" id="cropAndUploadBtn">
                    <i class="fa-solid fa-check me-1"></i> Save & Upload
                </button>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
let cropper = null;
const imageInput = document.getElementById('imageInput');
const imageToCrop = document.getElementById('imageToCrop');
const cropModalElement = document.getElementById('cropModal');
const cropModal = new bootstrap.Modal(cropModalElement);

// Select Image
imageInput.addEventListener('change', function (e) {
    const files = e.target.files;
    if (files && files.length > 0) {
        const reader = new FileReader();
        reader.onload = function (event) {
            imageToCrop.src = event.target.result;
            cropModal.show();
        };
        reader.readAsDataURL(files[0]);
    }
});

// Initialize Cropper on Modal Open
cropModalElement.addEventListener('shown.bs.modal', function () {
    if (cropper) {
        cropper.destroy();
    }
    cropper = new Cropper(imageToCrop, {
        aspectRatio: 1,
        viewMode: 1,
        autoCropArea: 0.8
    });
});

// Destroy Cropper on Modal Close
cropModalElement.addEventListener('hidden.bs.modal', function () {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    imageInput.value = '';
});

// Crop and Save Action
document.getElementById('cropAndUploadBtn').addEventListener('click', function () {
    if (!cropper) return;

    // Get Cropped Canvas
    const canvas = cropper.getCroppedCanvas({
        width: 300,
        height: 300
    });

    if (canvas) {
        const base64Image = canvas.toDataURL('image/png');
        
        // Form field me value set karke submit karein
        document.getElementById('croppedImageInput').value = base64Image;
        document.getElementById('cropForm').submit();
    }
});
</script>
</body>
</html>