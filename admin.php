<?php
include 'db.php';
session_start();

// Enable Error Reporting for Debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($pdo)) {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

// -------------------------------------------------------------------
// 1. HANDLE POST ACTIONS (Add Book, Update Stock, Update Order Status)
// -------------------------------------------------------------------
$flash_msg = '';
$flash_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // A. ADD NEW BOOK
    if (isset($_POST['action_add_book'])) {
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $publisher = trim($_POST['publisher'] ?? '');
        $mrp = floatval($_POST['mrp'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        // File Upload Logic (image1 to image5 support)
        $img_names = ['', '', '', '', ''];
        for ($i = 1; $i <= 5; $i++) {
            $file_key = 'image' . $i;
            if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION);
                $new_filename = 'book_' . time() . '_' . $i . '_' . rand(100, 999) . '.' . $ext;
                $target = 'uploads/' . $new_filename;

                if (!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }

                if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $target)) {
                    $img_names[$i - 1] = $new_filename;
                }
            }
        }

        if (!empty($title) && $price > 0) {
            try {
                $stmt = $pdo->prepare("INSERT INTO new_books (title, author, category, publisher, mrp, price, stock, description, image1, image2, image3, image4, image5) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $author, $category, $publisher, $mrp, $price, $stock, $description, $img_names[0], $img_names[1], $img_names[2], $img_names[3], $img_names[4]]);
                $flash_msg = "Book '{$title}' added successfully!";
                $flash_type = "success";
            } catch (PDOException $e) {
                $flash_msg = "Failed to add book: " . $e->getMessage();
                $flash_type = "danger";
            }
        } else {
            $flash_msg = "Please provide valid Title and Price!";
            $flash_type = "warning";
        }
    }

    // B. UPDATE ORDER STATUS
    if (isset($_POST['action_update_order_status'])) {
        $order_id = intval($_POST['order_id']);
        $new_status = trim($_POST['order_status']);
        try {
            $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);
            $flash_msg = "Order #{$order_id} status updated to '{$new_status}'!";
            $flash_type = "info";
        } catch (PDOException $e) {
            $flash_msg = "Failed to update status: " . $e->getMessage();
            $flash_type = "danger";
        }
    }

    // C. UPDATE STOCK QUICKLY
    if (isset($_POST['action_update_stock'])) {
        $book_id = intval($_POST['book_id']);
        $new_stock = intval($_POST['stock']);
        try {
            $stmt = $pdo->prepare("UPDATE new_books SET stock = ? WHERE id = ?");
            $stmt->execute([$new_stock, $book_id]);
            $flash_msg = "Stock updated successfully!";
            $flash_type = "info";
        } catch (PDOException $e) {
            $flash_msg = "Failed to update stock: " . $e->getMessage();
            $flash_type = "danger";
        }
    }
}

// -------------------------------------------------------------------
// 2. FETCH ANALYTICS & STATS
// -------------------------------------------------------------------

// Total Revenue & Total Orders
$stat_revenue = $pdo->query("SELECT SUM(final_price) AS total FROM orders WHERE order_status != 'Cancelled'")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$stat_orders_count = $pdo->query("SELECT COUNT(*) AS total FROM orders")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Monthly Sales Performance (Current Month)
$current_month_revenue = $pdo->query("SELECT SUM(final_price) AS total FROM orders WHERE order_status != 'Cancelled' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$current_month_orders = $pdo->query("SELECT COUNT(*) AS total FROM orders WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Yearly Sales Performance (Current Year)
$current_year_revenue = $pdo->query("SELECT SUM(final_price) AS total FROM orders WHERE order_status != 'Cancelled' AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$current_year_orders = $pdo->query("SELECT COUNT(*) AS total FROM orders WHERE YEAR(created_at) = YEAR(CURRENT_DATE())")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Total Books Count & Low Stock Alert Count (< 5)
$stat_books_count = $pdo->query("SELECT COUNT(*) AS total FROM new_books")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$stat_low_stock_count = $pdo->query("SELECT COUNT(*) AS total FROM new_books WHERE stock <= 5")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Top Performing Books (Most Sold)
$top_books_stmt = $pdo->query("
    SELECT b.title, b.author, b.price, COUNT(o.id) as total_orders, SUM(o.final_price) as total_earned 
    FROM orders o 
    JOIN new_books b ON o.book_id = b.id 
    WHERE o.order_status != 'Cancelled' 
    GROUP BY o.book_id 
    ORDER BY total_orders DESC 
    LIMIT 5
");
$top_books = $top_books_stmt->fetchAll(PDO::FETCH_ASSOC);

// -------------------------------------------------------------------
// 3. FETCH DATA TABLES
// -------------------------------------------------------------------

// Fetch All Orders with Book Title Details

$orders_stmt = $pdo->query("
    SELECT o.*, b.title as book_title, b.price as book_price 
    FROM orders o 
    LEFT JOIN books b ON o.book_id = b.id 
    ORDER BY o.id DESC
");
$all_orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);


$new_orders_stmt = $pdo->query("
    SELECT o.*, b.title as book_title, b.price as book_price 
    FROM new_book_orders o 
    LEFT JOIN new_books b ON o.book_id = b.id 
    ORDER BY o.id DESC
");
$all_orders_newbook = $new_orders_stmt->fetchAll(PDO::FETCH_ASSOC);



// Fetch All Books
$books_stmt = $pdo->query("SELECT * FROM new_books ORDER BY id DESC");
$all_books = $books_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GaonCart Admin Dashboard</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-bg: #f4f6f9;
            --sidebar-bg: #0d5c3a;
            --card-radius: 16px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--primary-bg);
            color: #2c3e50;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .stat-card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .card-custom {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            background: #ffffff;
        }

        .table-custom th {
            background-color: #f8f9fa;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            border-bottom: 2px solid #edf2f7;
        }

        .table-custom td {
            vertical-align: middle;
            font-size: 0.92rem;
        }

        .badge-status-pending { background-color: #fff3cd; color: #856404; }
        .badge-status-completed { background-color: #d4edda; color: #155724; }
        .badge-status-shipped { background-color: #cce5ff; color: #004085; }
        .badge-status-cancelled { background-color: #f8d7da; color: #721c24; }

        .img-thumbnail-small {
            width: 45px;
            height: 55px;
            object-fit: cover;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<!-- TOP NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm py-3">
    <div class="container-fluid px-4">
        <a class="navbar-brand text-success fs-4" href="#">
            <i class="fa-solid fa-book-open-reader me-2"></i>GaonCart <span class="text-white fw-light fs-6">| Admin Console</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-success rounded-pill fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addBookModal">
                <i class="fa-solid fa-plus me-1"></i> Add New Book
            </button>
            <a href="new_books.php" class="btn btn-outline-light rounded-pill btn-sm px-3" target="_blank">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Visit Store
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 my-4">

    <!-- FLASH MESSAGES -->
    <?php if(!empty($flash_msg)): ?>
        <div class="alert alert-<?= $flash_type ?> alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-info me-2"></i> <?= htmlspecialchars($flash_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- 1. KEY METRICS & OVERVIEW CARDS -->
    <div class="row g-3 mb-4">
        <!-- Total Revenue -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Total Lifetime Revenue</span>
                        <h3 class="fw-extrabold my-1 text-dark">₹<?= number_format($stat_revenue, 2) ?></h3>
                        <small class="text-muted"><i class="fa-solid fa-receipt me-1"></i> <?= $stat_orders_count ?> Total Orders</small>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-indian-rupee-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Sales -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">This Month's Sales</span>
                        <h3 class="fw-extrabold my-1 text-primary">₹<?= number_format($current_month_revenue, 2) ?></h3>
                        <small class="text-primary fw-semibold"><i class="fa-solid fa-calendar-check me-1"></i> <?= $current_month_orders ?> Orders this month</small>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yearly Sales -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">This Year's Sales</span>
                        <h3 class="fw-extrabold my-1 text-info">₹<?= number_format($current_year_revenue, 2) ?></h3>
                        <small class="text-info fw-semibold"><i class="fa-solid fa-calendar me-1"></i> <?= $current_year_orders ?> Orders in <?= date('Y') ?></small>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Health -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Inventory Status</span>
                        <h3 class="fw-extrabold my-1 text-dark"><?= $stat_books_count ?> Books</h3>
                        <?php if($stat_low_stock_count > 0): ?>
                            <small class="text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> <?= $stat_low_stock_count ?> Low Stock Items</small>
                        <?php else: ?>
                            <small class="text-success fw-bold"><i class="fa-solid fa-circle-check me-1"></i> Stock Health Healthy</small>
                        <?php endif; ?>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- TOP PERFORMING BOOKS -->
        <div class="col-lg-5">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-fire text-danger me-2"></i>Top Performing Books</h5>
                <?php if(count($top_books) > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach($top_books as $index => $tb): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge bg-light text-dark border rounded-circle fs-6" style="width:30px; height:30px; line-height:20px;">#<?= $index + 1 ?></span>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($tb['title']) ?></h6>
                                        <small class="text-muted">By <?= htmlspecialchars($tb['author']) ?></small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold text-success d-block">₹<?= number_format($tb['total_earned'], 2) ?></span>
                                    <small class="badge bg-success bg-opacity-10 text-success"><?= $tb['total_orders'] ?> Sold</small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted my-auto text-center py-4">No order analytics data available yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- QUICK SUMMARY BREAKDOWN -->
        <div class="col-lg-7">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-chart-pie text-primary me-2"></i>Sales Performance Highlights</h5>
                <div class="row g-3 my-auto">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-bold d-block mb-1">Average Order Value (AOV)</span>
                            <h4 class="fw-bold text-dark mb-0">
                                ₹<?= $stat_orders_count > 0 ? number_format($stat_revenue / $stat_orders_count, 2) : '0.00' ?>
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-bold d-block mb-1">Current Month Contribution</span>
                            <h4 class="fw-bold text-primary mb-0">
                                <?= $stat_revenue > 0 ? number_format(($current_month_revenue / $stat_revenue) * 100, 1) : '0' ?>%
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-bold d-block mb-1">Pending Orders</span>
                            <?php 
                                $pending_cnt = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'")->fetchColumn();
                            ?>
                            <h4 class="fw-bold text-warning mb-0"><?= $pending_cnt ?> Orders</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small fw-bold d-block mb-1">Out of Stock Books</span>
                            <?php 
                                $out_cnt = $pdo->query("SELECT COUNT(*) FROM new_books WHERE stock <= 0")->fetchColumn();
                            ?>
                            <h4 class="fw-bold text-danger mb-0"><?= $out_cnt ?> Titles</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. ORDERS MANAGEMENT TABLE -->
    <div class="card card-custom mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-cart-shopping me-2 text-success"></i>Customer Old Books Orders</h5>
            <span class="badge bg-secondary rounded-pill"><?= count($all_orders) ?> Orders Total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle table-custom mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Order ID</th>
                        <th>Customer Info</th>
                        <th>Address & Village</th>
                        <th>Book Name</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($all_orders) > 0): ?>
                        <?php foreach($all_orders as $ord): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?= $ord['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                    <small class="text-muted"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($ord['customer_phone']) ?></small>
                                </td>
                                <td>
                                    <div class="text-dark small fw-semibold"><?= htmlspecialchars($ord['customer_village'] ?? 'N/A') ?></div>
                                    <small class="text-muted d-block text-truncate" style="max-width: 200px;"><?= htmlspecialchars($ord['delivery_address']) ?></small>
                                </td>
                                <td class="fw-semibold text-dark">
                                    <?= htmlspecialchars($ord['book_title'] ?? 'Book ID: '.$ord['book_id']) ?>
                                </td>
                                <td class="fw-bold text-success">₹<?= number_format($ord['final_price'], 2) ?></td>
                                <td>
                                    <?php 
                                        $status = $ord['order_status'] ?? 'Pending';
                                        $badge_cls = 'badge-status-pending';
                                        if($status == 'Completed' || $status == 'Delivered') $badge_cls = 'badge-status-completed';
                                        if($status == 'Shipped') $badge_cls = 'badge-status-shipped';
                                        if($status == 'Cancelled') $badge_cls = 'badge-status-cancelled';
                                    ?>
                                    <span class="badge <?= $badge_cls ?> px-3 py-2 rounded-pill"><?= $status ?></span>
                                </td>
                                <td class="pe-4 text-end">
                                    <form method="POST" class="d-inline-flex gap-1">
                                        <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                        <select name="order_status" class="form-select form-select-sm rounded-pill" style="width: 120px;" onchange="this.form.submit()">
                                            <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Shipped" <?= $status == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                            <option value="Delivered" <?= $status == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                            <option value="Cancelled" <?= $status == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                        <input type="hidden" name="action_update_order_status" value="1">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No orders found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. new book ORDERS MANAGEMENT TABLE -->

     <div class="card card-custom mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-cart-shopping me-2 text-success"></i>Customer New Books Orders</h5>
            <span class="badge bg-secondary rounded-pill"><?= count($all_orders_newbook) ?> Orders Total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle table-custom mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Order ID</th>
                        <th>Customer Info</th>
                        <th>Address & Village</th>
                        <th>Book Name</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($all_orders_newbook) > 0): ?>
                        <?php foreach($all_orders_newbook as $ord): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?= $ord['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($ord['buyer_name']) ?></div>
                                    <small class="text-muted"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($ord['phone']) ?></small>
                                </td>
                                <td>
                                    <div class="text-dark small fw-semibold"><?= htmlspecialchars($ord['village'] ?? 'N/A') ?></div>
                                    <small class="text-muted d-block text-truncate" style="max-width: 200px;"><?= htmlspecialchars($ord['full_address']) ?></small>
                                </td>
                                <td class="fw-semibold text-dark">
                                    <?= htmlspecialchars($ord['book_title'] ?? 'Book ID: '.$ord['book_id']) ?>
                                </td>
                                <td class="fw-bold text-success">₹<?= number_format($ord['total_amount'], 2) ?></td>
                                <td>
                                    <?php 
                                        $status = $ord['order_status'] ?? 'Pending';
                                        $badge_cls = 'badge-status-pending';
                                        if($status == 'Completed' || $status == 'Delivered') $badge_cls = 'badge-status-completed';
                                        if($status == 'Shipped') $badge_cls = 'badge-status-shipped';
                                        if($status == 'Cancelled') $badge_cls = 'badge-status-cancelled';
                                    ?>
                                    <span class="badge <?= $badge_cls ?> px-3 py-2 rounded-pill"><?= $status ?></span>
                                </td>
                                <td class="pe-4 text-end">
                                    <form method="POST" class="d-inline-flex gap-1">
                                        <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                        <select name="order_status" class="form-select form-select-sm rounded-pill" style="width: 120px;" onchange="this.form.submit()">
                                            <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Shipped" <?= $status == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                            <option value="Delivered" <?= $status == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                            <option value="Cancelled" <?= $status == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                        <input type="hidden" name="action_update_order_status" value="1">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No orders found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- 3. BOOKS INVENTORY MANAGEMENT TABLE -->
    <div class="card card-custom">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-book me-2 text-primary"></i>Books Inventory</h5>
            <button class="btn btn-sm btn-success rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#addBookModal">
                <i class="fa-solid fa-plus me-1"></i> Add Book
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle table-custom mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Image</th>
                        <th>Title & Author</th>
                        <th>Category</th>
                        <th>MRP / Price</th>
                        <th>Stock Level</th>
                        <th class="pe-4 text-end">Quick Stock Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($all_books) > 0): ?>
                        <?php foreach($all_books as $bk): ?>
                            <tr>
                                <td class="ps-4">
                                    <?php if(!empty($bk['image1']) && file_exists('uploads/'.$bk['image1'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($bk['image1']) ?>" class="img-thumbnail-small border" alt="Cover">
                                    <?php else: ?>
                                        <div class="bg-light border rounded img-thumbnail-small d-flex align-items-center justify-content-center text-muted small"><i class="fa-solid fa-book"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($bk['title']) ?></div>
                                    <small class="text-muted">Author: <?= htmlspecialchars($bk['author'] ?? 'N/A') ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border rounded-pill"><?= htmlspecialchars($bk['category'] ?? 'General') ?></span>
                                </td>
                                <td>
                                    <span class="text-decoration-line-through text-muted small me-1">₹<?= number_format($bk['mrp'], 0) ?></span>
                                    <strong class="text-success">₹<?= number_format($bk['price'], 0) ?></strong>
                                </td>
                                <td>
                                    <?php if($bk['stock'] > 5): ?>
                                        <span class="badge bg-success-subtle text-success border border-success px-3 py-1 rounded-pill fw-bold"><?= $bk['stock'] ?> In Stock</span>
                                    <?php elseif($bk['stock'] > 0): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-1 rounded-pill fw-bold"><?= $bk['stock'] ?> Low Stock</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-1 rounded-pill fw-bold">Out of Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <form method="POST" class="d-inline-flex gap-2">
                                        <input type="hidden" name="book_id" value="<?= $bk['id'] ?>">
                                        <input type="number" name="stock" value="<?= $bk['stock'] ?>" class="form-control form-control-sm text-center rounded-3" style="width: 80px;" min="0">
                                        <button type="submit" name="action_update_stock" class="btn btn-sm btn-outline-primary rounded-3">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No books added yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ADD BOOK MODAL -->
<div class="modal fade" id="addBookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-book-medical me-2 text-success"></i>Add New Book to Store</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted">Book Title *</label>
                            <input type="text" name="title" class="form-control" required placeholder="Enter book title">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Category / Standard</label>
                            <input type="text" name="category" class="form-control" placeholder="e.g. Std 10, Novel">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Author Name</label>
                            <input type="text" name="author" class="form-control" placeholder="Author name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Publisher</label>
                            <input type="text" name="publisher" class="form-control" placeholder="Publisher name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">MRP (₹)</label>
                            <input type="number" step="0.01" name="mrp" class="form-control" placeholder="0.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Selling Price (₹) *</label>
                            <input type="number" step="0.01" name="price" class="form-control" required placeholder="0.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Initial Stock *</label>
                            <input type="number" name="stock" class="form-control" value="10" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Book Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Provide details about condition, content, etc."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Primary Cover Image (Image 1)</label>
                            <input type="file" name="image1" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Additional Image 2</label>
                            <input type="file" name="image2" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4">
                    <button type="button" class="btn btn-light rounded-3 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="action_add_book" class="btn btn-success rounded-3 fw-bold px-4">
                        <i class="fa-solid fa-check me-1"></i> Save & Publish Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>