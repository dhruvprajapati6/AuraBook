<?php
include 'db.php';
session_start();

// --- 1. LANGUAGE SETTING ---
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

$words = [
    'en' => [
        'author'        => 'Author (Lekhak)',
        'condition'     => 'Book Condition',
        'price_title'   => 'Price',
        'del_charge'    => '(With Delivery Charge)',
        'order_now_btn' => '🛒 Order Karein',
        'form_title'    => '📦 Order Details',
        'form_note'     => 'Cash on Delivery (COD) available for your village.',
        'cust_name'     => 'Aapka Poora Naam *',
        'cust_name_ph'  => 'Naam dalein',
        'cust_phone'    => 'Mobile Number *',
        'cust_phone_ph' => '10 digit mobile number',
        'cust_village'  => 'Aapka Gaon (Village) *',
        'cust_address'  => 'Ghar ka pata / Land-mark *',
        'cust_address_ph'=> 'Ghar nambar, gali, ya landmark...',
        'confirm_btn'   => 'Confirm Order (COD)',
        'not_found'     => 'Kitab nahi mili!'
    ],
    'gu' => [
        'author'        => 'લેખક (Author)',
        'condition'     => 'ચોપડીની હાલની સ્થિતિ',
        'price_title'   => 'કિંમત',
        'del_charge'    => '(ડીલીવરી ચાર્જ સાથે)',
        'order_now_btn' => '🛒 ઓર્ડર કરો',
        'form_title'    => '📦 ઓર્ડર ની વિગત',
        'form_note'     => 'તમારા ગામ માટે કેશ ઓન ડીલીવરી (COD) ઉપલબ્ધ છે.',
        'cust_name'     => 'તમારું પૂરું નામ *',
        'cust_name_ph'  => 'નામ લખો',
        'cust_phone'    => 'મોબાઈલ નંબર *',
        'cust_phone_ph' => '૧૦ આંકડાનો મોબાઈલ નંબર',
        'cust_village'  => 'તમારું ગામ *',
        'cust_address'  => 'ઘરનું સરનામું / લેન્ડમાર્ક *',
        'cust_address_ph'=> 'ઘર નંબર, શેરી, અથવા મુખ્ય જગ્યા...',
        'confirm_btn'   => 'ઓર્ડર કન્ફર્મ કરો (COD)',
        'not_found'     => 'ચોપડી મળી નથી!'
    ]
];

$l = $words[$current_lang];

if (!isset($_GET['id'])) {
    header("Location: index.php?lang=" . $current_lang);
    exit();
}

// Fetch Book Details
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$_GET['id']]);
$book = $stmt->fetch();

if (!$book) {
    die($l['not_found']);
}

$final_price = $book['price'] + 20;

// Logged in user auto-fill values
$default_name    = $_SESSION['user_name'] ?? '';
$default_phone   = $_SESSION['user_phone'] ?? '';
$default_village = $_SESSION['village_name'] ?? '';

// --- Dynamic Slider Images ---


$slides = [];
for ($i = 1; $i <= 5; $i++) {
    $img_key = 'image' . $i;
    if (!empty($book[$img_key]) && file_exists('uploads/newbook' . $book[$img_key])) {
        $slides[] = 'uploads/newbook' . $book[$img_key];
    }
}

if (empty($slides)) {
    $slides[] = 'https://via.placeholder.com/500x400?text=No+Image+Available';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']) ?> - Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .carousel-item img {
            height: 380px;
            object-fit: contain;
            background-color: #ffffff;
            border-radius: 12px;
        }
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 18px;
            border-radius: 50%;
        }
        .detail-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }
        .btn-order-now {
            background-color: #198754;
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
            padding: 14px;
            border-radius: 10px;
            transition: all 0.2s ease-in-out;
        }
        .btn-order-now:hover {
            background-color: #146c43;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-success shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand fw-bold fs-3" href="index.php?lang=<?= $current_lang ?>"> AuraBook</a>
        
        <div class="btn-group btn-group-sm shadow-sm">
            <a href="?id=<?= $book['id'] ?>&lang=en" class="btn <?= $current_lang == 'en' ? 'btn-light' : 'btn-outline-light' ?> fw-bold">EN</a>
            <a href="?id=<?= $book['id'] ?>&lang=gu" class="btn <?= $current_lang == 'gu' ? 'btn-light' : 'btn-outline-light' ?> fw-bold">ગુજ</a>
        </div>
    </div>
</nav>

<div class="container my-4" style="max-width: 900px;">

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show fw-bold" role="alert">
            ❌ Order process karne me error aayi. Kripya firse try karein.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- BOOK DETAILS ONLY CARD -->
    <div class="card detail-card p-4 bg-white">
        <div class="row g-4">
            
            <!-- Left Column: Image Slider -->
              <div class="col-md-6">
                <div id="bookCarousel" class="carousel slide border rounded-4 overflow-hidden" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($slides as $index => $img_src): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <img src="<?= htmlspecialchars($img_src) ?>" class="d-block w-100" alt="Book Image">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($slides) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#bookCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#bookCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!--<div class="col-md-6">
                <div id="bookCarousel" class="carousel slide border rounded-3 overflow-hidden shadow-sm" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($slides as $index => $img_src): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <img src="<?= htmlspecialchars($img_src) ?>" class="d-block w-100" alt="Book Image">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($slides) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#bookCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#bookCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>-->

            <!-- Right Column: Book Specifications -->
            <div class="col-md-6 d-flex flex-column justify-content-between">
                <div>
                    <span class="badge bg-secondary mb-2 fs-6"><i class="fa-solid fa-location-dot me-1"></i> <?= htmlspecialchars($book['village']) ?></span>
                    <h2 class="fw-bold text-dark mb-2"><?= htmlspecialchars($book['title']) ?></h2>
                    
                    <p class="text-muted mb-1 fs-6"><?= $l['author'] ?>: <strong class="text-dark"><?= htmlspecialchars($book['author']) ?></strong></p>
                    <p class="text-muted mb-3 fs-6"><?= $l['condition'] ?>: <span class="badge bg-info text-dark fs-6"><?= htmlspecialchars($book['book_condition']) ?></span></p>
                    
                    <hr>

                    <h2 class="text-success fw-bold mb-0">₹<?= $final_price ?> <span class="fs-6 text-muted fw-normal"><?= $l['del_charge'] ?></span></h2>
                    
                    <div class="mt-3 text-secondary">
                        <h6 class="fw-bold text-dark">Description:</h6>
                        <p class="small"><?= nl2br(htmlspecialchars($book['description'])) ?></p>
                    </div>
                </div>

                <!-- MAIN TRIGGER BUTTON FOR POPUP MODAL -->
                <div class="mt-4">
                    <button type="button" class="btn btn-order-now w-100 shadow" data-bs-toggle="modal" data-bs-target="#orderModal">
                        <?= $l['order_now_btn'] ?> (₹<?= $final_price ?>)
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>


<!-- ================= POPUP ORDER MODAL ================= -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            
            <!-- POPUP HEADER WITH UPPER-RIGHT CROSS (❎) BUTTON -->
            <div class="modal-header bg-success text-white border-0 py-3 rounded-top-4">
                <h5 class="modal-title fw-bold" id="orderModalLabel"><i class="fa-solid fa-cart-shopping me-2"></i><?= $l['form_title'] ?></h5>
                <!-- Upper right close button -->
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="d-flex align-items-center bg-light p-2 rounded mb-3 border">
                    <div class="me-3">
                        <img src="<?= htmlspecialchars($slides[0]) ?>" style="width: 50px; height: 60px; object-fit: cover; border-radius: 6px;">
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0"><?= htmlspecialchars($book['title']) ?></h6>
                        <small class="text-success fw-bold">Total Amount: ₹<?= $final_price ?></small>
                    </div>
                </div>

                <p class="text-muted small mb-3"><i class="fa-solid fa-truck-fast me-1"></i> <?= $l['form_note'] ?></p>

                <!-- FORM TARGETING BACKEND (place_order.php) -->
                <form action="place_order.php" method="POST">
                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                    <input type="hidden" name="final_price" value="<?= $final_price ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold small"><?= $l['cust_name'] ?></label>
                        <input type="text" name="cust_name" class="form-control" value="<?= htmlspecialchars($default_name) ?>" required placeholder="<?= $l['cust_name_ph'] ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small"><?= $l['cust_phone'] ?></label>
                        <input type="tel" name="cust_phone" class="form-control" value="<?= htmlspecialchars($default_phone) ?>" pattern="[0-9]{10}" required placeholder="<?= $l['cust_phone_ph'] ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small"><?= $l['cust_village'] ?></label>
                        <select name="cust_village" class="form-select" required>
                            <option value="">Gaon Chunein</option>
                            <option value="Gola" <?= $default_village == 'Gola' ? 'selected' : '' ?>>Gola</option>
                            <option value="Jalotra" <?= $default_village == 'Jalotra' ? 'selected' : '' ?>>Jalotra</option>
                            <option value="Talepura" <?= $default_village == 'Talepura' ? 'selected' : '' ?>>Talepura</option>
                            <option value="Pimpli" <?= $default_village == 'Pimpli' ? 'selected' : '' ?>>Pimpli</option>
                            <option value="Chandanpur" <?= $default_village == 'Chandanpur' ? 'selected' : '' ?>>Chandanpur</option>
                            <option value="Rampur" <?= $default_village == 'Rampur' ? 'selected' : '' ?>>Rampur</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small"><?= $l['cust_address'] ?></label>
                        <textarea name="cust_address" class="form-control" rows="2" required placeholder="<?= $l['cust_address_ph'] ?>"></textarea>
                    </div>

                    <button type="submit" name="place_order" class="btn btn-success w-100 fw-bold py-2 fs-5 rounded-3">
                        <?= $l['confirm_btn'] ?>
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>