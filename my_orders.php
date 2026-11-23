<?php
include 'db.php';
session_start();

// --- 1. LANGUAGE SETTING ---
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'gu'; // Default to Gujarati

$words = [
    'en' => [
        'title'         => 'My Orders - AuraBook',
        'heading'       => '📦 My Orders',
        'subheading'    => 'Enter your Mobile Number & Email to view order status.',
        'phone_ph'      => '10 Digit Mobile Number',
        'email_ph'      => 'Email Address',
        'search_btn'    => 'Find Orders',
        'no_orders'     => 'No orders found with this Mobile Number & Email combination.',
        'sec_msg'       => 'Enter registered Mobile Number & Email to track your orders.',
        'amount'        => 'Total Price (Inc. Delivery)',
        'status'        => 'Order Status',
        'success_alert' => '🎉 Order Placed Successfully! Order ID: #'
    ],
    'gu' => [
        'title'         => 'મારા ઓર્ડર - ઓરાબુક',
        'heading'       => '📦 મારા ઓર્ડર્સ',
        'subheading'    => 'ઓર્ડર સ્ટેટસ જોવા માટે મોબાઈલ નંબર અને ઈમેલ દાખલ કરો.',
        'phone_ph'      => '૧૦ આંકડાનો મોબાઈલ નંબર',
        'email_ph'      => 'ઈમેલ એડ્રેસ (Email)',
        'search_btn'    => 'ઓર્ડર શોધો',
        'no_orders'     => 'આ મોબાઈલ નંબર અને ઈમેલ સાથે કોઈ ઓર્ડર મળ્યો નથી.',
        'sec_msg'       => 'તમારા ઓર્ડર ટ્રેક કરવા માટે તમારો રજિસ્ટર્ડ મોબાઈલ અને ઈમેલ લખો.',
        'amount'        => 'કુલ કિંમત (ડીલીવરી સાથે)',
        'status'        => 'ઓર્ડર સ્ટેટસ',
        'success_alert' => '🎉 તમારો ઓર્ડર કન્ફર્મ થઈ ગયો છે! ઓર્ડર ID: #'
    ]
];

$l = $words[$current_lang];

// Input Fetch Logic (Get/Post/Session)
$customer_phone = '';
$customer_email = '';

// Check GET inputs from Search Form
if (!empty($_GET['phone']) && !empty($_GET['email'])) {
    $customer_phone = trim($_GET['phone']);
    $customer_email = trim($_GET['email']);
    $_SESSION['last_customer_phone'] = $customer_phone;
    $_SESSION['last_customer_email'] = $customer_email;
} 
// Session fallback if available
elseif (isset($_SESSION['last_customer_phone']) && isset($_SESSION['last_customer_email'])) {
    $customer_phone = $_SESSION['last_customer_phone'];
    $customer_email = $_SESSION['last_customer_email'];
} 
elseif (isset($_SESSION['user_phone']) && isset($_SESSION['user_email'])) {
    $customer_phone = $_SESSION['user_phone'];
    $customer_email = $_SESSION['user_email'];
}

// Fetch Orders ONLY if BOTH Phone & Email are provided and match DB records
$orders = [];
if (!empty($customer_phone) && !empty($customer_email)) {
    $stmt = $pdo->prepare("
        SELECT 
            orders.*, 
            books.title AS book_title, 
            books.author AS book_author, 
            books.image1 AS book_image 
        FROM orders 
        LEFT JOIN books ON orders.book_id = books.id 
        WHERE orders.customer_phone = ? AND orders.customer_email = ? 
        ORDER BY orders.id DESC
    ");
    $stmt->execute([$customer_phone, $customer_email]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $l['title'] ?></title>
    
    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Noto+Sans+Gujarati:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', 'Noto Sans Gujarati', sans-serif;
            background-color: #f4f7f6; 
            color: #2d3748;
        }
        .order-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
        }
        .book-thumb {
            width: 75px;
            height: 90px;
            object-fit: cover;
            border-radius: 10px;
        }
        .search-box-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }
        .badge-status-pending { background-color: #ffc107; color: #000; }
        .badge-status-confirmed { background-color: #0d6efd; color: #fff; }
        .badge-status-shipped { background-color: #0dcaf0; color: #000; }
        .badge-status-delivered { background-color: #198754; color: #fff; }
        .badge-status-cancelled { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-success shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand fw-bold fs-4" href="index.php?lang=<?= $current_lang ?>"><i class="fa-solid fa-book "></i>&nbsp;&nbsp;GaonCart</a>
        
        <!-- Language Switcher -->
        <div class="btn-group bg-black bg-opacity-25 p-1 rounded-pill">
            <a href="?lang=gu" class="btn btn-sm rounded-pill px-3 fw-bold <?= $current_lang == 'gu' ? 'btn-warning text-dark' : 'text-white' ?>">ગુજરાતી</a>
            <a href="?lang=en" class="btn btn-sm rounded-pill px-3 fw-bold <?= $current_lang == 'en' ? 'btn-warning text-dark' : 'text-white' ?>">English</a>
        </div>
    </div>
</nav>

<div class="container my-4" style="max-width: 980px;">

    <!-- SUCCESS ALERT AFTER ORDER PLACEMENT -->
    <?php if (isset($_GET['status']) && $_GET['status'] == 'success' && isset($_GET['order_id'])): ?>
        <div class="alert alert-success alert-dismissible fade show p-3 rounded-4 shadow-sm border-0 mb-4" role="alert">
            <h5 class="alert-heading fw-bold mb-1"><i class="fa-solid fa-circle-check me-2"></i><?= $l['success_alert'] ?><?= htmlspecialchars($_GET['order_id']) ?></h5>
            <p class="mb-0 small">Aapka order successfully register ho gaya hai.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- PAGE HEADER & SEARCH FORM CARD -->
    <div class="search-box-card p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-4">
                <h4 class="fw-bold text-dark mb-1"><?= $l['heading'] ?></h4>
                <p class="text-muted small mb-0"><?= $l['subheading'] ?></p>
            </div>
            
            <!-- DUAL INPUT SEARCH FORM (PHONE + EMAIL) -->
            <div class="col-lg-8">
                <form action="my_orders.php" method="GET" class="row g-2 align-items-center">
                    <input type="hidden" name="lang" value="<?= $current_lang ?>">
                    
                    <!-- Phone Input -->
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-phone"></i></span>
                            <input type="tel" name="phone" class="form-control border-start-0" placeholder="<?= $l['phone_ph'] ?>" value="<?= htmlspecialchars($customer_phone) ?>" pattern="[0-9]{10}" required>
                        </div>
                    </div>

                    <!-- Email Input -->
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control border-start-0" placeholder="<?= $l['email_ph'] ?>" value="<?= htmlspecialchars($customer_email) ?>" required>
                        </div>
                    </div>

                    <!-- Search Button -->
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success fw-bold w-100 py-2 shadow-sm">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> <?= $l['search_btn'] ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ORDERS LIST DISPLAY -->
    <?php if (!empty($orders)): ?>
        
        <div class="row g-3">
            <?php foreach ($orders as $ord): ?>
                <?php
                    // Dynamic Badge Logic for Order Status
                    $status = strtolower($ord['order_status'] ?? 'pending');
                    $badge_class = 'badge-status-pending';
                    if ($status == 'confirmed') $badge_class = 'badge-status-confirmed';
                    elseif ($status == 'shipped') $badge_class = 'badge-status-shipped';
                    elseif ($status == 'delivered') $badge_class = 'badge-status-delivered';
                    elseif ($status == 'cancelled') $badge_class = 'badge-status-cancelled';

                    // Book Image Fallback
                    $book_img = 'https://via.placeholder.com/75x90?text=No+Img';
                    if (!empty($ord['book_image']) && file_exists('uploads/' . $ord['book_image'])) {
                        $book_img = 'uploads/' . $ord['book_image'];
                    }

                    // Date Formatting
                    $order_date = isset($ord['created_at']) ? date('d M Y, h:i A', strtotime($ord['created_at'])) : 'N/A';
                ?>

                <div class="col-12">
                    <div class="card order-card p-3 bg-white">
                        <div class="row align-items-center g-3">
                            
                            <!-- Book Image & Details -->
                            <div class="col-md-4 d-flex align-items-center gap-3">
                                <img src="<?= htmlspecialchars($book_img) ?>" class="book-thumb border shadow-sm" alt="Book Image">
                                <div>
                                    <span class="text-muted small fw-semibold">Order #<?= $ord['id'] ?></span>
                                    <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($ord['book_title'] ?? 'Kitab') ?></h6>
                                    <p class="text-muted small mb-1"><i class="fa-solid fa-user-pen me-1"></i> <?= htmlspecialchars($ord['book_author'] ?? 'Lekhak') ?></p>
                                    <small class="text-secondary"><i class="fa-regular fa-clock me-1"></i> <?= $order_date ?></small>
                                </div>
                            </div>

                            <!-- Customer Info & Address -->
                            <div class="col-md-3">
                                <span class="badge bg-light text-dark border mb-1">📍 <?= htmlspecialchars($ord['customer_village'] ?? 'Local') ?></span>
                                <p class="fw-bold mb-0 text-dark small"><?= htmlspecialchars($ord['customer_name']) ?></p>
                                <p class="text-muted small mb-0"><i class="fa-solid fa-phone me-1"></i> <?= htmlspecialchars($ord['customer_phone']) ?></p>
                                <small class="text-secondary d-block text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($ord['customer_email'] ?? '') ?>">
                                    <i class="fa-solid fa-envelope me-1"></i> <?= htmlspecialchars($ord['customer_email'] ?? '') ?>
                                </small>
                            </div>

                            <!-- Price -->
                            <div class="col-md-2 text-md-center">
                                <span class="text-muted small d-block"><?= $l['amount'] ?></span>
                                <h5 class="fw-bold text-success mb-0">₹<?= number_format($ord['final_price'], 2) ?></h5>
                                <small class="text-muted" style="font-size: 11px;">(Cash on Delivery)</small>
                            </div>

                            <!-- Status Badge -->
                            <div class="col-md-3 text-md-end">
                                <span class="text-muted small d-block mb-1"><?= $l['status'] ?></span>
                                <span class="badge <?= $badge_class ?> fs-6 px-3 py-2 rounded-pill shadow-sm fw-bold">
                                    <i class="fa-solid fa-truck-ramp-box me-1"></i> <?= ucfirst($ord['order_status']) ?>
                                </span>
                            </div>

                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>

    <!-- IF SEARCH RETURNED NO RESULTS -->
    <?php elseif (!empty($customer_phone) && !empty($customer_email)): ?>
        <div class="card p-5 text-center border-0 shadow-sm bg-white rounded-4 my-4">
            <div class="text-muted mb-3">
                <i class="fa-solid fa-triangle-exclamation fa-3x text-warning"></i>
            </div>
            <h5 class="fw-bold text-dark"><?= $l['no_orders'] ?></h5>
            <p class="text-muted small">Mobile: <strong><?= htmlspecialchars($customer_phone) ?></strong> | Email: <strong><?= htmlspecialchars($customer_email) ?></strong></p>
            <div>
                <a href="index.php?lang=<?= $current_lang ?>" class="btn btn-success fw-bold px-4 rounded-3">🛒 Kitab Kharedein</a>
            </div>
        </div>

    <!-- INITIAL STATE (NOT YET SEARCHED) -->
    <?php else: ?>
        <div class="card p-5 text-center border-0 shadow-sm bg-white rounded-4 my-4">
            <div class="text-muted mb-3">
                <i class="fa-solid fa-user-shield fa-3x text-success"></i>
            </div>
            <h5 class="fw-bold text-dark"><?= $l['sec_msg'] ?></h5>
            <p class="text-muted small">Suraksha (security) ke liye mobile number aur email address dono sahi bharna zaroori hai.</p>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>