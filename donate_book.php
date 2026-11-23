<?php
include 'db.php';
session_start();

// Login check: Sirf logged-in user hi book donate/sell kar sakta hai
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Form Submit Handling
if (isset($_POST['submit_book'])) {
    $book_title    = trim($_POST['book_title']);
    $author_name   = trim($_POST['author_name']);
    $category      = $_POST['category'];
    $book_condition= $_POST['book_condition'];
    $book_type     = $_POST['book_type']; // 'Free Donate' or 'Sell'
    $book_price    = ($book_type == 'Sell') ? floatval($_POST['book_price']) : 0.00;
    $description   = trim($_POST['description']);
    $phone_number  = trim($_POST['phone_number']);
    $location      = trim($_POST['location']);

    // Image Upload Logic
    $image_name = '';
    if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0) {
        $target_dir = "uploads/books/";
        
        // Check if folder exists, if not create it
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES["book_image"]["name"], PATHINFO_EXTENSION);
        $image_name = "book_" . time() . "_" . rand(1000, 9999) . "." . $file_extension;
        $target_file = $target_dir . $image_name;

        // Valid extensions
        $allowed_types = array('jpg', 'jpeg', 'png', 'webp');
        if (!in_array(strtolower($file_extension), $allowed_types)) {
            $error = "❌ Sirf JPG, JPEG, PNG ya WEBP image upload karein.";
        } else {
            move_uploaded_file($_FILES["book_image"]["tmp_name"], $target_file);
        }
    }

    if (empty($error)) {
        $stmt = $pdo->prepare("INSERT INTO donated_books (user_id, book_title, author_name, category, book_condition, book_type, book_price, description, book_image, phone_number, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$user_id, $book_title, $author_name, $category, $book_condition, $book_type, $book_price, $description, $image_name, $phone_number, $location])) {
            $success = "🎉 Aapki book successfully add ho gayi hai!";
        } else {
            $error = "❌ Kuch gadbad hui, kripya dubara try karein.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="gu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate / Sell Old Books - KitabBazar</title>
    
    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { 
            background-color: #f4f7f6; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #2d3748;
        }
        .donate-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            border: none;
        }
        .form-control:focus, .form-select:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.15);
        }
        .image-preview {
            width: 100%;
            height: 180px;
            border: 2px dashed #ced4da;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            cursor: pointer;
            overflow: hidden;
            position: relative;
        }
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-success shadow-sm">
    <div class="container d-flex justify-content-between">
        <a class="navbar-brand fw-bold fs-4" href="index.php"><i class="fa-solid fa-book"></i>&nbsp;&nbsp;AuraBook</a>
        <a href="index.php" class="btn btn-sm btn-outline-light fw-bold">
            <i class="fa-solid fa-house me-1"></i> Dashboard
        </a>
    </div>
</nav>

<div class="container my-5" style="max-width: 650px;">
    
    <div class="donate-card p-4 p-md-5">
        
        <div class="text-center mb-4">
            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill fs-6 mb-2">
                📖 Share Knowledge
            </span>
            <h3 class="fw-bold text-dark">Purani Kitab Donate / Sell Karein</h3>
            <p class="text-muted small">Apni purani kitabon ki details bharein taaki jaruratmand log iska faida utha sakein.</p>
        </div>

        <!-- ALERTS -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger rounded-3 fw-bold mb-4"><?= $error ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success rounded-3 fw-bold mb-4"><?= $success ?></div>
        <?php endif; ?>

        <!-- FORM -->
        <form action="donate_book.php" method="POST" enctype="multipart/form-data">
            
            <!-- Type Choice (Donate or Sell) -->
            <div class="mb-4">
                <label class="form-label fw-bold text-muted small">Aap kya karna chahte hain? *</label>
                <div class="d-flex gap-3">
                    <div class="form-check card flex-fill p-3 border-success border-2 shadow-sm">
                        <input class="form-check-input" type="radio" name="book_type" id="typeDonate" value="Free Donate" checked onchange="togglePriceInput()">
                        <label class="form-check-label fw-bold text-success" for="typeDonate">
                            🎁 Free Donate (Muft me dein)
                        </label>
                    </div>
                    <div class="form-check card flex-fill p-3 border">
                        <input class="form-check-input" type="radio" name="book_type" id="typeSell" value="Sell" onchange="togglePriceInput()">
                        <label class="form-check-label fw-bold text-dark" for="typeSell">
                            💰 Sell (Bechna hai)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Book Title & Author -->
            <div class="row g-3 mb-3">
                <div class="col-md-7">
                    <label class="form-label fw-bold text-muted small">Kitab Ka Naam (Book Title) *</label>
                    <input type="text" name="book_title" class="form-control rounded-3" placeholder="e.g. Science Standard 10" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold text-muted small">Lekhak (Author)</label>
                    <input type="text" name="author_name" class="form-control rounded-3" placeholder="e.g. GCERT / NCERT">
                </div>
            </div>

            <!-- Category & Condition -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Category *</label>
                   <select name="category" class="form-select" required>
    <option value="" disabled selected>Select Category / Class</option>
    <option value="Standard 1">Standard 1</option>
    <option value="Standard 2">Standard 2</option>
    <option value="Standard 3">Standard 3</option>
    <option value="Standard 4">Standard 4</option>
    <option value="Standard 5">Standard 5</option>
    <option value="Standard 6">Standard 6</option>
    <option value="Standard 7">Standard 7</option>
    <option value="Standard 8">Standard 8</option>
    <option value="Standard 9">Standard 9</option>
    <option value="Standard 10">Standard 10</option>
    <option value="Standard 11">Standard 11</option>
    <option value="Standard 12">Standard 12</option>
    <option value="College">College</option>
    <option value="Competitive">Competitive</option>
    <option value="General">Story Book</option>
</select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Condition *</label>
                    <select name="book_condition" class="form-select rounded-3" required>
                        <option value="Like New">Like New (Ekdum Nayi)</option>
                        <option value="Good">Good (Achhi condition)</option>
                        <option value="Fair">Fair (Thodi purani)</option>
                    </select>
                </div>
            </div>

            <!-- Price Input (Hidden by Default) -->
            <div class="mb-3" id="priceContainer" style="display: none;">
                <label class="form-label fw-bold text-muted small">Keemat (Price in ₹) *</label>
                <div class="input-group">
                    <span class="input-group-text bg-light fw-bold">₹</span>
                    <input type="number" name="book_price" id="book_price" class="form-control" placeholder="100" min="0" step="5">
                </div>
            </div>

            <!-- Book Photo Upload -->
            <div class="mb-3">
                <label class="form-label fw-bold text-muted small">Kitab Ki Photo Upload Karein *</label>
                <div class="image-preview" onclick="document.getElementById('book_image').click()">
                    <div class="text-center" id="uploadPlaceholder">
                        <i class="fa-solid fa-cloud-arrow-up fs-2 text-success mb-2"></i>
                        <p class="mb-0 small fw-bold text-muted">Click karke Photo select karein</p>
                    </div>
                    <img id="imagePreviewTag" src="#" alt="Book Image">
                </div>
                <input type="file" name="book_image" id="book_image" accept="image/*" class="d-none" onchange="previewImage(this)" required>
            </div>

            <!-- Mobile & City/Location -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Contact Mobile Number *</label>
                    <input type="tel" name="phone_number" class="form-control rounded-3" placeholder="10 Digit Number" pattern="[0-9]{10}" value="<?= htmlspecialchars($_SESSION['user_phone'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small">Aapka Shehar / Gaon (Location) *</label>
                    <input type="text" name="location" class="form-control rounded-3" placeholder="e.g. Ahmedabad, Maninagar" required>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="form-label fw-bold text-muted small">Ekstra Information (Optional)</label>
                <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Kitab ke bare me thoda aur likhein..."></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="submit_book" class="btn btn-success w-100 fw-bold py-3 shadow-sm rounded-3">
                <i class="fa-solid fa-paper-plane me-2"></i> Book Submit Karein
            </button>

        </form>

    </div>

</div>

<!-- JavaScript for Dynamic Functions -->
<script>
// Toggle Price Box Based on Choice
function togglePriceInput() {
    const isSell = document.getElementById('typeSell').checked;
    const priceBox = document.getElementById('priceContainer');
    const priceInput = document.getElementById('book_price');

    if (isSell) {
        priceBox.style.display = 'block';
        priceInput.required = true;
    } else {
        priceBox.style.display = 'none';
        priceInput.required = false;
        priceInput.value = '';
    }
}

// Live Image Preview function
function previewImage(input) {
    const preview = document.getElementById('imagePreviewTag');
    const placeholder = document.getElementById('uploadPlaceholder');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>