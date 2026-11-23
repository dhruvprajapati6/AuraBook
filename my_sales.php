<?php
include 'db.php';
session_start();

// Login check Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Logged-in user ki saari bechi/listed books nikalo
$stmt = $pdo->prepare("SELECT * FROM books WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$my_sales = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meri Bechi Hui Books - AuraBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-success"><i class="fa-solid fa-book-bookmark me-2"></i>Meri Bechi Hui / Listed Books</h3>
        <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Home Par Jayein</a>
    </div>

    <?php if (empty($my_sales)): ?>
        <div class="card p-5 text-center shadow-sm border-0">
            <i class="fa-solid fa-book-open fa-3x text-muted mb-3"></i>
            <h5>Aapne abhi tak koi book bechne ke liye nahi dali hai.</h5>
            <p class="text-muted">Aap apni purani kitabein yahan se bech sakte hain.</p>
            <div>
                <a href="sell.php" class="btn btn-warning fw-bold">+ Book Bechein</a>
            </div>
        </div>
    <?php else: ?>
        <div class="table-responsive bg-white rounded shadow-sm p-3">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Book Image</th>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Village</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($my_sales as $book): ?>
                        <tr>
                            <td>
                                <?php if (!empty($book['image1'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($book['image1']) ?>" style="width: 50px; height: 60px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/50x60" style="width: 50px; height: 60px; object-fit: cover;">
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($book['title']) ?></td>
                            <td class="text-success fw-bold">₹<?= htmlspecialchars($book['price']) ?></td>
                            <td>📍 <?= htmlspecialchars($book['village']) ?></td>
                            <td>
                                <?php if ($book['status'] == 'available'): ?>
                                    <span class="badge bg-success">Available (Bikne ke liye tayar)</span>
                                <?php elseif ($book['status'] == 'sold'): ?>
                                    <span class="badge bg-danger">Bik Gayi (Sold Out)</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Under Review</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?= date('d M Y', strtotime($book['created_at'] ?? 'now')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>