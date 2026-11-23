<?php 
include 'db.php'; 
session_start();

// 1. LANGUAGE SETTING
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'gu';

$words = [
    'en' => [
        'site_title' => 'AuraBook',
        'page_title' => '✨ Brand New Books (Fresh Stock)',
        'page_subtitle' => '100% original books directly sourced from publishers with heavy discounts.',
        'home_btn' => 'Home',
        'search_placeholder' => 'Search new books by title, author, subject...',
        'all_categories' => 'All Categories / Standards',
        'filter_btn' => 'Search',
        'reset_btn' => 'Reset',
        'no_books' => 'No new books available in stock right now. New stock coming soon!',
        'author' => 'Author',
        'publisher' => 'Original',
        'stock_left' => 'In Stock',
        'off' => 'OFF',
        'buy_now' => '⚡ Buy New Book',
        'used_store' => 'Looking for Used Books?',
        'used_link' => 'Browse Used Books Store',
        'total_found' => 'New Books Available'
    ],
    'gu' => [
        'site_title' => 'ઓરાબુક',
        'page_title' => '✨ બિલકુલ નવી ચોપડીઓ (Fresh Stock)',
        'page_subtitle' => 'સીધા પબ્લિશર પાસેથી મંગાવેલી ૧૦૦% નવી અને ઓરિજિનલ બુક્સ ભારે ડિસ્કાઉન્ટ સાથે.',
        'home_btn' => 'હોમ',
        'search_placeholder' => 'નવી ચોપડીનું નામ, લેખક કે વિષય શોધો...',
        'all_categories' => 'બધા ધોરણ / કેટેગરી',
        'filter_btn' => 'શોધો',
        'reset_btn' => 'ફરીથી સેટ કરો',
        'no_books' => 'અત્યારે નવી ચોપડીઓનો સ્ટોક ઉપલબ્ધ નથી. ટૂંક સમયમાં નવો સ્ટોક આવશે!',
        'author' => 'લેખક',
        'publisher' => 'ઓરિજિનલ',
        'stock_left' => 'સ્ટોક બાકી',
        'off' => 'છૂટ',
        'buy_now' => '⚡ નવી બુક ખરીદો',
        'used_store' => 'જૂની ચોપડીઓ શોધવાના છો?',
        'used_link' => 'જૂની ચોપડીઓનો સ્ટોર જુઓ',
        'total_found' => 'નવી બુક્સ ઉપલબ્ધ'
    ]
];

$l = $words[$current_lang];

// 2. SEARCH & FILTER LOGIC
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$cat_filter = isset($_GET['cat']) ? trim($_GET['cat']) : '';

$sql = "SELECT * FROM new_books WHERE stock > 0";
$params = [];

if (!empty($cat_filter)) {
    $sql .= " AND (category = ? OR title LIKE ?)";
    $params[] = $cat_filter;
    $params[] = "%$cat_filter%";
}

if (!empty($search_query)) {
    $sql .= " AND (title LIKE ? OR author LIKE ? OR category LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$new_books = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $l['page_title'] ?> - KitabBazar</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Noto+Sans+Gujarati:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --kb-blue: #0d5c3a;
            --kb-blue-dark: #198754;
            --kb-yellow: #ffc107;
            --kb-bg: #f4f7f5;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Noto Sans Gujarati', sans-serif;
            background-color: var(--kb-bg);
            color: #2b2b2b;
            padding-bottom: 80px;
        }

        .custom-navbar {
            background: rgba(13, 92, 58, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            
        }

        .filter-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            border-top: 5px solid var(--kb-blue);
        }

        .book-card {
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .book-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 30px rgba(13, 110, 253, 0.15) !important;
        }

        .book-img-wrapper {
            position: relative;
            height: 230px;
            background: #f8f9fa;
            overflow: hidden;
        }

        .book-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .book-card:hover .book-img-wrapper img {
            transform: scale(1.05);
        }

        .badge-new {
            position: absolute;
            top: 12px;
            left: 12px;
            background: var(--kb-blue);
            color: #fff;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
        }

        .badge-discount {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #dc3545;
            color: white;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 800;
        }

        .stock-tag {
            font-size: 0.75rem;
            font-weight: 600;
            color: #198754;
            background: #e8f5e9;
            padding: 2px 8px;
            border-radius: 6px;
        }

        /* Mobile Bottom Nav */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            z-index: 1000;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }

        .mobile-bottom-nav a {
            color: #666;
            text-decoration: none;
            font-size: 0.75rem;
            text-align: center;
            font-weight: 600;
        }

        .mobile-bottom-nav a.active {
            color: var(--kb-blue);
        }

        .mobile-bottom-nav i {
            font-size: 1.2rem;
            display: block;
            margin-bottom: 2px;
        }

        @media (min-width: 992px) {
            .mobile-bottom-nav { display: none; }
            body { padding-bottom: 0; }
        }
    </style>
</head>
<body>

<!-- TOP NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark custom-navbar sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand fw-extrabold fs-3 d-flex align-items-center gap-2" href="index.php">
            <span><i class="fa-solid fa-book "></i>&nbsp;&nbsp;<?= $l['site_title'] ?></span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <!-- Language Switcher -->
            <div class="btn-group bg-black bg-opacity-25 p-1 rounded-pill">
                <a href="?lang=gu<?= !empty($search_query) ? '&search='.$search_query : '' ?><?= !empty($cat_filter) ? '&cat='.$cat_filter : '' ?>" class="btn btn-sm rounded-pill px-3 fw-bold <?= $current_lang == 'gu' ? 'btn-warning text-dark' : 'text-white' ?>">ગુજરાતી</a>
                <a href="?lang=en<?= !empty($search_query) ? '&search='.$search_query : '' ?><?= !empty($cat_filter) ? '&cat='.$cat_filter : '' ?>" class="btn btn-sm rounded-pill px-3 fw-bold <?= $current_lang == 'en' ? 'btn-warning text-dark' : 'text-white' ?>">English</a>
            </div>

            <a href="used_books.php" class="btn btn-outline-light btn-sm rounded-pill px-3 d-none d-sm-inline-block">
                <i class="fa-solid fa-book-open me-1"></i> Used Store
            </a>

            <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="fa-solid fa-house me-1"></i> <?= $l['home_btn'] ?>
            </a>
        </div>
    </div>
</nav>

<div class="container my-4">

    <!-- HEADER & SEARCH SECTION -->
    <div class="filter-card p-4 mb-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-success mb-1"><?= $l['page_title'] ?></h2>
            <p class="text-muted small mb-0"><?= $l['page_subtitle'] ?></p>
        </div>

        <form method="GET" action="new_books.php" class="row g-2 justify-content-center">
            <?php if(!empty($_GET['lang'])): ?>
                <input type="hidden" name="lang" value="<?= htmlspecialchars($_GET['lang']) ?>">
            <?php endif; ?>

            <!-- Search Keyword -->
            <div class="col-lg-5 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="<?= $l['search_placeholder'] ?>" value="<?= htmlspecialchars($search_query) ?>">
                </div>
            </div>

            <!-- Category Filter -->
            <div class="col-lg-4 col-md-4 col-8">
<select name="cat" class="form-select bg-light">
    <option value=""><?= $l['all_categories'] ?></option>
    <option value="Standard 1" <?= $cat_filter == 'Standard 1' ? 'selected' : '' ?>>Class 01st (૦૧)</option>
    <option value="Standard 2" <?= $cat_filter == 'Standard 2' ? 'selected' : '' ?>>Class 02nd (૦૨)</option>
    <option value="Standard 3" <?= $cat_filter == 'Standard 3' ? 'selected' : '' ?>>Class 03rd (૦૩)</option>
    <option value="Standard 4" <?= $cat_filter == 'Standard 4' ? 'selected' : '' ?>>Class 04th (૦૪)</option>
    <option value="Standard 5" <?= $cat_filter == 'Standard 5' ? 'selected' : '' ?>>Class 05th (૦૫)</option>
    <option value="Standard 6" <?= $cat_filter == 'Standard 6' ? 'selected' : '' ?>>Class 06th (૦૬)</option>
    <option value="Standard 7" <?= $cat_filter == 'Standard 7' ? 'selected' : '' ?>>Class 07th (૦૭)</option>
    <option value="Standard 8" <?= $cat_filter == 'Standard 8' ? 'selected' : '' ?>>Class 08th (૦૮)</option>
    <option value="Standard 9" <?= $cat_filter == 'Standard 9' ? 'selected' : '' ?>>Class 09th (૦૯)</option>
    <option value="Standard 10" <?= $cat_filter == 'Standard 10' ? 'selected' : '' ?>>Class 10th (૧૦)</option>
    <option value="Standard 11" <?= $cat_filter == 'Standard 11' ? 'selected' : '' ?>>Class 11th (૧૧)</option>
    <option value="Standard 12" <?= $cat_filter == 'Standard 12' ? 'selected' : '' ?>>Class 12th (૧૨)</option>
    <option value="College" <?= $cat_filter == 'College' ? 'selected' : '' ?>>College / ITI</option>
    <option value="Competitive" <?= $cat_filter == 'Competitive' ? 'selected' : '' ?>>Competitive Exam</option>
</select>
            </div>

            <!-- Submit & Reset -->
            <div class="col-lg-2 col-md-2 col-4 d-flex gap-1">
                <button type="submit" class="btn btn-success fw-bold w-100 rounded-3">
                    <i class="fa-solid fa-filter me-1"></i> <?= $l['filter_btn'] ?>
                </button>
                <?php if(!empty($search_query) || !empty($cat_filter)): ?>
                    <a href="new_books.php" class="btn btn-outline-secondary rounded-3" title="<?= $l['reset_btn'] ?>">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- COUNT BADGE & LINK -->
    <div class="d-flex justify-content-between align-items-center mb-3 px-2">
        <span class="fw-bold text-dark fs-5">
            <i class="fa-solid fa-sparkles text-primary me-2"></i><?= count($new_books) ?> <?= $l['total_found'] ?>
        </span>
        <a href="used_books.php" class="text-decoration-none text-success fw-bold small">
            <?= $l['used_store'] ?> <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>
    </div>

    <!-- NEW BOOKS GRID -->
    <div class="row g-3 mb-5">
        <?php if(empty($new_books)): ?>
            <div class="col-12 text-center my-5 py-5 bg-white rounded-4 shadow-sm">
                <i class="fa-solid fa-box-open text-muted fs-1 mb-3"></i>
                <p class="text-muted fs-5 mb-0"><?= $l['no_books'] ?></p>
                <a href="new_books.php" class="btn btn-link text-primary fw-bold mt-2">Clear search & view all</a>
            </div>
        <?php else: ?>
            <?php foreach($new_books as $book): ?>
                <?php 
                    // Calculate Discount Percentage
                    $mrp = floatval($book['mrp'] ?? 0);
                    $price = floatval($book['price'] ?? 0);
                    $discount_percent = 0;
                    if($mrp > $price && $mrp > 0) {
                        $discount_percent = round((($mrp - $price) / $mrp) * 100);
                    }
                ?>
                <div class="col-xl-3 col-lg-4 col-sm-6 col-12">
                    <div class="card h-100 book-card shadow-sm">
                        
                        <div class="book-img-wrapper">
                            <?php if(!empty($book['image1'])): ?>
                                <img src="admin/uploads/<?= htmlspecialchars($book['image1']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" loading="lazy">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x220?text=New+Book" alt="New Book Cover">
                            <?php endif; ?>
                            
                            <span class="badge-new">NEW</span>

                            <?php if($discount_percent > 0): ?>
                                <span class="badge-discount"><?= $discount_percent ?>% <?= $l['off'] ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body d-flex flex-column p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-light text-primary border align-self-start">
                                    <?= htmlspecialchars($book['category'] ?? 'General') ?>
                                </span>
                                <?php if(isset($book['stock']) && $book['stock'] > 0): ?>
                                    <span class="stock-tag">
                                        <i class="fa-solid fa-check me-1"></i><?= $l['stock_left'] ?>: <?= $book['stock'] ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <h6 class="card-title fw-bold text-dark text-truncate mb-1" title="<?= htmlspecialchars($book['title']) ?>">
                                <?= htmlspecialchars($book['title']) ?>
                            </h6>
                            
                            <p class="card-text text-muted small mb-2 text-truncate">
                                <i class="fa-solid fa-user-pen me-1 opacity-50"></i><?= $l['author'] ?>: <?= htmlspecialchars($book['author'] ?? 'N/A') ?>
                            </p>
                            
                            <div class="mt-auto pt-2 border-top">
                                <div class="d-flex align-items-baseline gap-2 mb-2">
                                    <span class="fs-4 fw-extrabold text-success">₹<?= number_format($price, 0) ?></span>
                                    <?php if($mrp > $price): ?>
                                        <span class="text-muted text-decoration-line-through small">₹<?= number_format($mrp, 0) ?></span>
                                    <?php endif; ?>
                                </div>

                                <a href="buy_new_book.php?id=<?= $book['id'] ?>" class="btn btn-success w-100 fw-bold py-2 rounded-3">
                                    <?= $l['buy_now'] ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- MOBILE BOTTOM NAVIGATION -->
<div class="mobile-bottom-nav">
    <a href="index.php">
        <i class="fa-solid fa-house"></i> Home
    </a>
    <a href="used_books.php">
        <i class="fa-solid fa-book"></i> Used Books
    </a>
    <a href="login.php?redirect=sell.php">
        <i class="fa-solid fa-circle-plus text-success" style="font-size:1.5rem;"></i> Sell
    </a>
    <a href="new_books.php" class="active">
        <i class="fa-solid fa-book"></i> New Books
    </a>
    <a href="<?= isset($_SESSION['user_id']) ? 'profile.php' : 'login.php' ?>">
        <i class="fa-solid fa-user"></i> Account
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>