<?php

include 'db.php';
session_start();

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'gu'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
}

$current_lang = $_SESSION['lang'] ?? 'gu';

$words = [
    'en' => [
        'author' => 'Author',
        'condition' => 'Book Condition',
        'price_title' => 'Price',
        'del_charge' => 'Free Delivery Included',
        'order_now_btn' => '⚡ Buy Old Book Now',
        'form_title' => '📦 Place Your Order',
        'form_note' => 'Cash on Delivery (COD) is available in your village.',
        'cust_name' => 'Full Name',
        'cust_name_ph' => 'Enter your full name',
        'cust_phone' => 'Mobile Number',
        'cust_phone_ph' => '10 digit mobile number',
        'cust_email' => 'Email',
        'cust_email_ph' => 'Email Address',
        'cust_village' => 'Select Village / City',
        'cust_address' => 'Delivery Address / Landmark',
        'cust_address_ph' => 'House no, street, landmark...',
        'confirm_btn' => 'Confirm Order (COD)',
        'not_found' => 'Book Not Found!',
        'seller_location' => 'Seller Location',
        'close' => 'Close',
        'description' => 'Description',
        'no_description' => 'Old but good condition book is available.',
        'choose_village' => '-- Choose Village --'
    ],

    'gu' => [
        'author' => 'લેખક',
        'condition' => 'ચોપડીની સ્થિતિ',
        'price_title' => 'કિંમત',
        'del_charge' => 'ફ્રી ડિલિવરી સાથે',
        'order_now_btn' => '⚡ જૂની ચોપડી ખરીદો',
        'form_title' => '📦 ઓર્ડર ની વિગત',
        'form_note' => 'તમારા ગામ માટે કેશ ઓન ડીલીવરી (COD) ઉપલબ્ધ છે.',
        'cust_name' => 'તમારું પૂરું નામ',
        'cust_name_ph' => 'નામ લખો',
        'cust_phone' => 'મોબાઈલ નંબર',
        'cust_phone_ph' => '૧૦ આંકડાનો મોબાઈલ નંબર',
        'cust_email' => 'ઈમેલ',
        'cust_email_ph' => 'ઈમેલ એડ્રેસ',
        'cust_village' => 'ગામ પસંદ કરો',
        'cust_address' => 'ઘરનું સરનામું / લેન્ડમાર્ક',
        'cust_address_ph' => 'ઘર નંબર, શેરી, અથવા મુખ્ય જગ્યા...',
        'confirm_btn' => 'ઓર્ડર કન્ફર્મ કરો (COD)',
        'not_found' => 'ચોપડી મળી નથી!',
        'seller_location' => 'વેચનારનું ગામ',
        'close' => 'બંધ કરો',
        'description' => 'વર્ણન',
        'no_description' => 'જૂની પરંતુ સારી સ્થિતિની ચોપડી ઉપલબ્ધ છે.',
        'choose_village' => '-- ગામ પસંદ કરો --'
    ]
];

$l = $words[$current_lang];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?lang=" . urlencode($current_lang));
    exit;
}

$book_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    die(
        '<div style="max-width:600px;margin:50px auto;padding:25px;background:#f8d7da;color:#842029;border-radius:10px;text-align:center;font-family:Arial;">'
        . htmlspecialchars($l['not_found']) .
        '</div>'
    );
}

$book_price = (float)($book['price'] ?? 0);
$final_price = $book_price + 20;

$default_name = $_SESSION['user_name'] ?? '';
$default_phone = $_SESSION['user_phone'] ?? '';
$default_village = $_SESSION['village_name'] ?? '';
$default_email = $_SESSION['user_email'] ?? '';

$slides = [];

$imageFolder = 'uploads/oldbook/';
$imageFolderPath = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'oldbook' . DIRECTORY_SEPARATOR;

for ($i = 1; $i <= 5; $i++) {

    $imgKey = 'image' . $i;

    if (!empty($book[$imgKey])) {

        $filename = trim($book[$imgKey]);
        $filename = basename($filename);

        $fullPath = $imageFolderPath . $filename;
        $imageUrl = $imageFolder . rawurlencode($filename);

        if (is_file($fullPath)) {
            $slides[] = $imageUrl;
        }
    }
}

if (empty($slides)) {
    $slides[] = 'https://via.placeholder.com/600x450?text=No+Image+Available';
}

$villages = [
    "Gola",
    "Jalotra",
    "Talepura",
    "Pimpli",
    "Chandanpur",
    "Rampur"
];

?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($current_lang, ENT_QUOTES, 'UTF-8') ?>">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= htmlspecialchars($book['title'] ?? 'Book') ?> - AuraBook</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Noto+Sans+Gujarati:wght@400;600;700&display=swap" rel="stylesheet">

<style>

:root {
    --kb-green:#198754;
    --kb-green-hover:#146c43;
    --kb-bg:#f4f7f6;
}

* {
    box-sizing:border-box;
}

body {
    font-family:'Plus Jakarta Sans','Noto Sans Gujarati',sans-serif;
    background-color:var(--kb-bg);
    color:#2d3748;
}

.navbar-brand {
    letter-spacing:.3px;
}

.detail-card {
    background:#fff;
    border-radius:20px;
    border:none;
    box-shadow:0 10px 30px rgba(0,0,0,.05);
    overflow:hidden;
}

#bookCarousel {
    background:#fafafa;
}

.book-image {
    width:100%;
    height:380px;
    object-fit:contain;
    display:block;
    background:#fafafa;
}

.carousel-control-prev,
.carousel-control-next {
    width:55px;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-color:rgba(0,0,0,.65);
    width:42px;
    height:42px;
    padding:10px;
    border-radius:50%;
    background-size:50%;
}

.price-badge-box {
    background:#e8f5e9;
    border-left:4px solid var(--kb-green);
    padding:15px;
    border-radius:12px;
}

.btn-order-now {
    background-color:var(--kb-green);
    color:#fff;
    font-size:1.15rem;
    font-weight:700;
    padding:14px;
    border-radius:12px;
    transition:all .3s ease;
    box-shadow:0 4px 14px rgba(25,135,84,.3);
}

.btn-order-now:hover {
    background-color:var(--kb-green-hover);
    color:#fff;
    transform:translateY(-2px);
    box-shadow:0 6px 20px rgba(25,135,84,.4);
}

.condition-badge {
    font-size:.85rem;
    padding:6px 12px;
    border-radius:20px;
}

.modal-content {
    border-radius:18px;
    overflow:hidden;
}

@media (max-width:767px) {

    .book-image {
        height:300px;
    }

    .detail-card {
        border-radius:15px;
    }

    .modal-dialog {
        margin:10px;
    }
}

</style>

</head>

<body>

<nav class="navbar navbar-dark bg-success shadow-sm">

<div class="container d-flex justify-content-between align-items-center">

<a
class="navbar-brand fw-bold fs-4"
href="index.php?lang=<?= urlencode($current_lang) ?>"
>
AuraBook
</a>

<div class="btn-group bg-black bg-opacity-25 p-1 rounded-pill">

<a
href="?id=<?= $book_id ?>&lang=gu"
class="btn btn-sm rounded-pill px-3 fw-bold <?= $current_lang === 'gu' ? 'btn-warning text-dark' : 'text-white' ?>"
>
ગુજરાતી
</a>

<a
href="?id=<?= $book_id ?>&lang=en"
class="btn btn-sm rounded-pill px-3 fw-bold <?= $current_lang === 'en' ? 'btn-warning text-dark' : 'text-white' ?>"
>
English
</a>

</div>

</div>

</nav>

<div class="container my-4 my-md-5" style="max-width:960px;">

<?php if (isset($_GET['error'])): ?>

<div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm fw-bold mb-4">

<i class="fa-solid fa-triangle-exclamation me-2"></i>

Order process karne me error aayi. Kripya firse try karein.

<button type="button" class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php endif; ?>

<div class="detail-card p-4">

<div class="row g-4">

<div class="col-md-6">

<div
id="bookCarousel"
class="carousel slide border rounded-4 overflow-hidden"
data-bs-ride="carousel"
data-bs-interval="4000"
>

<div class="carousel-inner">

<?php foreach ($slides as $index => $img_src): ?>

<div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">

<img
src="<?= htmlspecialchars($img_src, ENT_QUOTES, 'UTF-8') ?>"
class="d-block w-100 book-image"
alt="<?= htmlspecialchars($book['title'] ?? 'Book Image', ENT_QUOTES, 'UTF-8') ?>"
loading="<?= $index === 0 ? 'eager' : 'lazy' ?>"
onerror="this.onerror=null;this.src='https://via.placeholder.com/600x450?text=Image+Not+Found';"
>

</div>

<?php endforeach; ?>

</div>

<?php if (count($slides) > 1): ?>

<button
class="carousel-control-prev"
type="button"
data-bs-target="#bookCarousel"
data-bs-slide="prev"
>

<span class="carousel-control-prev-icon"></span>

<span class="visually-hidden">Previous</span>

</button>

<button
class="carousel-control-next"
type="button"
data-bs-target="#bookCarousel"
data-bs-slide="next"
>

<span class="carousel-control-next-icon"></span>

<span class="visually-hidden">Next</span>

</button>

<?php endif; ?>

</div>

<?php if (count($slides) > 1): ?>

<div class="text-center mt-2">

<small class="text-muted">

<i class="fa-regular fa-images me-1"></i>

<?= count($slides) ?> Images

</small>

</div>

<?php endif; ?>

</div>

<div class="col-md-6 d-flex flex-column justify-content-between">

<div>

<div class="mb-2">

<span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-semibold">

<i class="fa-solid fa-location-dot text-danger me-1"></i>

<?= $l['seller_location'] ?>:

<strong>

<?= htmlspecialchars($book['village'] ?? 'Local', ENT_QUOTES, 'UTF-8') ?>

</strong>

</span>

</div>

<h2 class="fw-bold text-dark mb-2">

<?= htmlspecialchars($book['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>

</h2>

<p class="text-muted mb-2">

<i class="fa-solid fa-pen-nib me-1"></i>

<?= $l['author'] ?>:

<strong class="text-dark">

<?= htmlspecialchars($book['author'] ?? '', ENT_QUOTES, 'UTF-8') ?>

</strong>

</p>

<p class="mb-3">

<span class="text-muted me-2">

<?= $l['condition'] ?>:

</span>

<span class="badge bg-info-subtle text-info-emphasis border border-info condition-badge fw-bold">

✨

<?= htmlspecialchars($book['book_condition'] ?? 'Good', ENT_QUOTES, 'UTF-8') ?>

</span>

</p>

<div class="price-badge-box mb-4">

<div class="d-flex align-items-baseline gap-2">

<span class="fs-2 fw-bold text-success">

₹<?= number_format($final_price, 0) ?>

</span>

<span class="text-muted text-decoration-line-through small">

₹<?= number_format($final_price + 30, 0) ?>

</span>

</div>

<small class="text-success fw-bold d-block mt-1">

<i class="fa-solid fa-truck-fast me-1"></i>

<?= $l['del_charge'] ?>

</small>

</div>

<div class="mb-3">

<h6 class="fw-bold text-dark mb-1">

<?= $l['description'] ?>:

</h6>

<p class="text-muted small">

<?php if (!empty($book['description'])): ?>

<?= nl2br(htmlspecialchars($book['description'], ENT_QUOTES, 'UTF-8')) ?>

<?php else: ?>

<?= $l['no_description'] ?>

<?php endif; ?>

</p>

</div>

</div>

<div>

<button
type="button"
class="btn btn-order-now w-100 border-0"
data-bs-toggle="modal"
data-bs-target="#orderModal"
>

<?= $l['order_now_btn'] ?>

</button>

</div>

</div>

</div>

</div>

</div>

<div
class="modal fade"
id="orderModal"
tabindex="-1"
aria-labelledby="orderModalLabel"
aria-hidden="true"
>

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content border-0 shadow-lg">

<div class="modal-header bg-success text-white border-0 py-3">

<h5 class="modal-title fw-bold" id="orderModalLabel">

<i class="fa-solid fa-cart-shopping me-2"></i>

<?= $l['form_title'] ?>

</h5>

<button
type="button"
class="btn-close btn-close-white"
data-bs-dismiss="modal"
aria-label="Close"
></button>

</div>

<div class="modal-body p-4">

<div class="d-flex align-items-center bg-light p-3 rounded-3 mb-3 border">

<img
src="<?= htmlspecialchars($slides[0], ENT_QUOTES, 'UTF-8') ?>"
style="width:55px;height:65px;object-fit:cover;"
class="rounded me-3"
alt="Book"
>

<div>

<h6 class="fw-bold mb-1 text-dark">

<?= htmlspecialchars($book['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>

</h6>

<span class="badge bg-success">

Total: ₹<?= number_format($final_price, 0) ?>

</span>

</div>

</div>

<p class="text-muted small mb-3">

<i class="fa-solid fa-shield-halved text-success me-1"></i>

<?= $l['form_note'] ?>

</p>

<form action="place_order.php" method="POST">

<input
type="hidden"
name="book_id"
value="<?= $book_id ?>"
>

<input
type="hidden"
name="final_price"
value="<?= htmlspecialchars($final_price, ENT_QUOTES, 'UTF-8') ?>"
>

<div class="mb-3">

<label class="form-label fw-bold small text-muted">

<?= $l['cust_name'] ?>

<span class="text-danger">*</span>

</label>

<input
type="text"
name="cust_name"
class="form-control"
value="<?= htmlspecialchars($default_name, ENT_QUOTES, 'UTF-8') ?>"
required
autocomplete="name"
placeholder="<?= htmlspecialchars($l['cust_name_ph'], ENT_QUOTES, 'UTF-8') ?>"
>

</div>

<div class="mb-3">

<label class="form-label fw-bold small text-muted">

<?= $l['cust_phone'] ?>

<span class="text-danger">*</span>

</label>

<input
type="tel"
name="cust_phone"
class="form-control"
value="<?= htmlspecialchars($default_phone, ENT_QUOTES, 'UTF-8') ?>"
pattern="[0-9]{10}"
minlength="10"
maxlength="10"
inputmode="numeric"
required
autocomplete="tel"
placeholder="<?= htmlspecialchars($l['cust_phone_ph'], ENT_QUOTES, 'UTF-8') ?>"
>

</div>

<div class="mb-3">

<label class="form-label fw-bold small text-muted">

<?= $l['cust_email'] ?>

<span class="text-danger">*</span>

</label>

<input
type="email"
name="cust_email"
class="form-control"
value="<?= htmlspecialchars($default_email, ENT_QUOTES, 'UTF-8') ?>"
required
autocomplete="email"
placeholder="<?= htmlspecialchars($l['cust_email_ph'], ENT_QUOTES, 'UTF-8') ?>"
>

</div>

<div class="mb-3">

<label class="form-label fw-bold small text-muted">

<?= $l['cust_village'] ?>

<span class="text-danger">*</span>

</label>

<select
name="cust_village"
class="form-select"
required
>

<option value="">

<?= $l['choose_village'] ?>

</option>

<?php foreach ($villages as $v): ?>

<option
value="<?= htmlspecialchars($v, ENT_QUOTES, 'UTF-8') ?>"
<?= $default_village === $v ? 'selected' : '' ?>
>

<?= htmlspecialchars($v, ENT_QUOTES, 'UTF-8') ?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="mb-4">

<label class="form-label fw-bold small text-muted">

<?= $l['cust_address'] ?>

<span class="text-danger">*</span>

</label>

<textarea
name="cust_address"
class="form-control"
rows="2"
required
autocomplete="street-address"
placeholder="<?= htmlspecialchars($l['cust_address_ph'], ENT_QUOTES, 'UTF-8') ?>"
></textarea>

</div>

<div class="d-flex gap-2">

<button
type="button"
class="btn btn-light rounded-3 fw-bold w-50"
data-bs-dismiss="modal"
>

❎ <?= $l['close'] ?>

</button>

<button
type="submit"
name="place_order"
class="btn btn-success rounded-3 fw-bold w-50 py-2"
>

<?= $l['confirm_btn'] ?>

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