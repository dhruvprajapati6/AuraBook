<?php
include 'db.php';
session_start();

// Language toggle support
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;

if ($book_id <= 0) {
    header("Location: index.php");
    exit();
}

// Fetch Book Details
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if (!$book) {
    echo "Book nahi mili!";
    exit();
}

// Calculate final price (Book price + delivery/platform fee)
$final_price = $book['price'] + 20;

// Logged-in user data pre-fill karne ke liye (agar logged in hai to)
$user_name = $_SESSION['user_name'] ?? '';
$user_phone = $_SESSION['user_phone'] ?? '';
$user_village = $_SESSION['village_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - KitabBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .checkout-card { max-width: 600px; margin: 30px auto; border-radius: 12px; border: none; }
    </style>
</head>
<body>

<div class="container">
    <div class="card checkout-card shadow-sm">
        <div class="card-header bg-success text-white py-3">
            <h4 class="mb-0 fw-bold"><i class="fa-solid fa-cart-shopping me-2"></i>Order Confirm Karein</h4>
        </div>
        <div class="card-body p-4">
            
            <!-- Book Summary Card -->
            <div class="d-flex align-items-center bg-light p-3 rounded mb-4 border">
                <?php if(!empty($book['image1'])): ?>
                    <img src="uploads/<?= htmlspecialchars($book['image1']) ?>" style="width: 70px; height: 85px; object-fit: cover; border-radius: 6px;" class="me-3">
                <?php else: ?>
                    <img src="https://via.placeholder.com/70x85" style="width: 70px; height: 85px; object-fit: cover; border-radius: 6px;" class="me-3">
                <?php endif; ?>
                <div>
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($book['title']) ?></h5>
                    <p class="text-muted small mb-1">Author: <?= htmlspecialchars($book['author']) ?></p>
                    <h5 class="text-success fw-bold mb-0">Total: ₹<?= $final_price ?> <span class="fs-6 text-muted fw-normal">(₹20 Delivery Charge Included)</span></h5>
                </div>
            </div>

            <!-- Customer Details Form -->
            <form action="place_order.php" method="POST">
                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                <input type="hidden" name="final_price" value="<?= $final_price ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold">Aapka Naam (Customer Name)</label>
                    <input type="text" name="customer_name" class="form-control" value="<?= htmlspecialchars($user_name) ?>" placeholder="Jaise: Mr. Rahul Sharma" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mobile Number (Customer Phone)</label>
                    <input type="tel" name="customer_phone" class="form-control" value="<?= htmlspecialchars($user_phone) ?>" placeholder="10 digit mobile number" pattern="[0-9]{10}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Gaon / Area (Customer Village)</label>
                    <select name="customer_village" class="form-select" required>
                        <option value="">Gaon Chunein</option>
                        <option value="Gola" <?= $user_village == 'Gola' ? 'selected' : '' ?>>Gola</option>
                        <option value="Jalotra" <?= $user_village == 'Jalotra' ? 'selected' : '' ?>>Jalotra</option>
                        <option value="Talepura" <?= $user_village == 'Talepura' ? 'selected' : '' ?>>Talepura</option>
                        <option value="Pimpli" <?= $user_village == 'Pimpli' ? 'selected' : '' ?>>Pimpli</option>
                        <option value="Chandanpur" <?= $user_village == 'Chandanpur' ? 'selected' : '' ?>>Chandanpur</option>
                        <option value="Rampur" <?= $user_village == 'Rampur' ? 'selected' : '' ?>>Rampur</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Poora Address (Delivery Address)</label>
                    <textarea name="delivery_address" class="form-control" rows="3" placeholder="Ghar / Dukaan ka pata aur Landmark likhein" required></textarea>
                </div>

                <button type="submit" name="submit_order" class="btn btn-success w-100 fw-bold py-2 fs-5">
                    Order Confirm Karein (Cash on Delivery)
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>