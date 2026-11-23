<?php

include 'db.php';
session_start();

if (empty($_SESSION['user_id'])) {
    header("Location: login.php?redirect=sell.php");
    exit;
}

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'gu'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$current_lang = $_SESSION['lang'] ?? 'en';

$words = [
    'en' => [
        'page_title' => '📚 Sell Your Book',
        'sec1_title' => '1. Book Information',
        'book_name' => 'Book Name *',
        'author' => 'Author',
        'price' => 'Your Price (₹) *',
        'price_note' => '₹20 will be automatically added for the customer.',
        'condition' => 'Book Condition *',
        'cond_good' => 'Good / Normal',
        'cond_new' => 'Like New',
        'cond_old' => 'Old (Torn)',
        'all_villages' => 'Which Village is the Book In? *',
        'category' => 'Select Your Class And Category',
        'photos' => 'Upload Book Photos (Max 5)',
        'desc' => 'Additional Details (Description)',
        'desc_ph' => 'E.g. pages missing, condition details...',
        'sec2_title' => '2. Your (Seller) Information',
        'sec2_note' => 'This information is private and only visible to the admin.',
        'seller_name' => 'Your Full Name *',
        'seller_phone' => 'Mobile Number (WhatsApp) *',
        'seller_village' => 'Your Village *',
        'submit_btn' => '📖 Submit Book for Sale',
        'back_home' => 'Back to Home'
    ],
    'gu' => [
        'page_title' => '📚 તમારી ચોપડી વેચો',
        'sec1_title' => '1. ચોપડીની માહિતી',
        'book_name' => 'ચોપડીનું નામ *',
        'author' => 'લેખક',
        'price' => 'તમારી કિંમત (₹) *',
        'price_note' => 'ગ્રાહકને આમાં ₹૨૦ ઓટોમેટીક પ્લસ થઈને દેખાશે.',
        'condition' => 'ચોપડીની સ્થિતિ *',
        'cond_good' => 'ઠીક ઠાક',
        'cond_new' => 'બિલકુલ નવી જેવી',
        'cond_old' => 'જૂની (થોડી ફાટેલી)',
        'all_villages' => 'ચોપડી કયા ગામમાં છે? *',
        'category' => 'ધોરણ / કેટેગરી પસંદ કરો',
        'photos' => 'ચોપડીના ફોટા અપલોડ કરો (મહત્તમ 5)',
        'desc' => 'અન્ય માહિતી (વર્ણન)',
        'desc_ph' => 'જેમ કે ફાટેલી છે, બધા પેજ છે કે નહીં...',
        'sec2_title' => '2. તમારી (વેચનારની) માહિતી',
        'sec2_note' => 'આ માહિતી ગુપ્ત રહેશે અને ફક્ત એડમિન જ જોઈ શકશે.',
        'seller_name' => 'તમારું પૂરું નામ *',
        'seller_phone' => 'મોબાઈલ નંબર (WhatsApp) *',
        'seller_village' => 'તમારું ગામ *',
        'submit_btn' => '📖 ચોપડી વેચવા માટે સબમિટ કરો',
        'back_home' => 'પાછા હોમ પેજ પર જાઓ'
    ]
];

$l = $words[$current_lang];

$message = '';

$session_user_id = $_SESSION['user_id'] ?? null;
$default_name = $_SESSION['user_name'] ?? '';
$default_phone = $_SESSION['user_phone'] ?? '';
$default_village = $_SESSION['village_name'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $condition = trim($_POST['condition'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $village = trim($_POST['village'] ?? '');
    $book_category = trim($_POST['book_category'] ?? '');

    $seller_name = trim($_POST['seller_name'] ?? '');
    $seller_phone = trim($_POST['seller_phone'] ?? '');
    $seller_village = trim($_POST['seller_village'] ?? '');

    $allowed_villages = [
        'Karnala',
        'Moteta',
        'Khodla',
        'Motahada',
        'Mumanvas',
        'Vasan',
        'Gola',
        'Jalotra',
        'Talepura',
        'Pimpli'
    ];

    $allowed_categories = [
        'Standard 1',
        'Standard 2',
        'Standard 3',
        'Standard 4',
        'Standard 5',
        'Standard 6',
        'Standard 7',
        'Standard 8',
        'Standard 9',
        'Standard 10',
        'Standard 11',
        'Standard 12',
        'College',
        'Competitive',
        'General'
    ];

    $allowed_conditions = [
        'Theek Thaak',
        'Bilkul Nayi Jaisi',
        'Purani (Thodi Fati)'
    ];

    if (
        $title === '' ||
        $price === '' ||
        $seller_name === '' ||
        $seller_phone === '' ||
        $village === '' ||
        $seller_village === '' ||
        $book_category === ''
    ) {
        $message = $current_lang === 'gu'
            ? '<div class="alert alert-warning fw-bold">કૃપા કરીને બધી જરૂરી માહિતી ભરો.</div>'
            : '<div class="alert alert-warning fw-bold">Kripya saari zaroori jankari bharein.</div>';
    } elseif (!is_numeric($price) || $price <= 0) {
        $message = $current_lang === 'gu'
            ? '<div class="alert alert-warning fw-bold">કૃપા કરીને સાચી કિંમત નાખો.</div>'
            : '<div class="alert alert-warning fw-bold">Please enter a valid price.</div>';
    } elseif (!preg_match('/^[0-9]{10}$/', $seller_phone)) {
        $message = $current_lang === 'gu'
            ? '<div class="alert alert-warning fw-bold">મોબાઈલ નંબર 10 આંકડાનો હોવો જોઈએ.</div>'
            : '<div class="alert alert-warning fw-bold">Mobile number must contain exactly 10 digits.</div>';
    } elseif (!in_array($village, $allowed_villages, true)) {
        $message = '<div class="alert alert-warning fw-bold">Invalid village selected.</div>';
    } elseif (!in_array($seller_village, $allowed_villages, true)) {
        $message = '<div class="alert alert-warning fw-bold">Invalid seller village selected.</div>';
    } elseif (!in_array($book_category, $allowed_categories, true)) {
        $message = '<div class="alert alert-warning fw-bold">Invalid category selected.</div>';
    } elseif (!in_array($condition, $allowed_conditions, true)) {
        $message = '<div class="alert alert-warning fw-bold">Invalid book condition.</div>';
    } else {

        $uploaded_images = [
            null,
            null,
            null,
            null,
            null
        ];

        $upload_dir = __DIR__ . '/uploads/oldbook/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $allowed_extensions = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];

        $allowed_mime_types = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        for ($i = 1; $i <= 5; $i++) {

            $field = 'image' . $i;

            if (
                isset($_FILES[$field]) &&
                $_FILES[$field]['error'] === UPLOAD_ERR_OK &&
                !empty($_FILES[$field]['tmp_name'])
            ) {

                $original_name = $_FILES[$field]['name'];
                $tmp_name = $_FILES[$field]['tmp_name'];
                $file_size = $_FILES[$field]['size'];

                $extension = strtolower(
                    pathinfo($original_name, PATHINFO_EXTENSION)
                );

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $tmp_name);
                finfo_close($finfo);

                if (
                    in_array($extension, $allowed_extensions, true) &&
                    in_array($mime_type, $allowed_mime_types, true) &&
                    $file_size <= 5 * 1024 * 1024
                ) {

                    $new_file_name =
                        date('YmdHis') .
                        '_img' .
                        $i .
                        '_' .
                        random_int(1000, 9999) .
                        '.' .
                        $extension;

                    $target_path = $upload_dir . $new_file_name;

                    if (move_uploaded_file($tmp_name, $target_path)) {
                        $uploaded_images[$i - 1] = $new_file_name;
                    }
                }
            }
        }

        $user_id = null;

        if (
            $session_user_id !== null &&
            $session_user_id !== '' &&
            ctype_digit((string)$session_user_id)
        ) {
            $check_user = $pdo->prepare(
                "SELECT id FROM users WHERE id = ? LIMIT 1"
            );

            $check_user->execute([
                (int)$session_user_id
            ]);

            $valid_user = $check_user->fetchColumn();

            if ($valid_user !== false) {
                $user_id = (int)$valid_user;
            }
        }

        try {

            if ($user_id !== null) {

                $sql = "
                    INSERT INTO books
                    (
                        user_id,
                        title,
                        author,
                        price,
                        book_condition,
                        description,
                        village,
                        category,
                        customer_name,
                        customer_phone,
                        customer_village,
                        image1,
                        image2,
                        image3,
                        image4,
                        image5
                    )
                    VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    $user_id,
                    htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($author, ENT_QUOTES, 'UTF-8'),
                    (float)$price,
                    htmlspecialchars($condition, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($village, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($book_category, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($seller_name, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($seller_phone, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($seller_village, ENT_QUOTES, 'UTF-8'),
                    $uploaded_images[0],
                    $uploaded_images[1],
                    $uploaded_images[2],
                    $uploaded_images[3],
                    $uploaded_images[4]
                ]);

            } else {

                $sql = "
                    INSERT INTO books
                    (
                        user_id,
                        title,
                        author,
                        price,
                        book_condition,
                        description,
                        village,
                        category,
                        customer_name,
                        customer_phone,
                        customer_village,
                        image1,
                        image2,
                        image3,
                        image4,
                        image5
                    )
                    VALUES
                    (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($author, ENT_QUOTES, 'UTF-8'),
                    (float)$price,
                    htmlspecialchars($condition, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($village, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($book_category, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($seller_name, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($seller_phone, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($seller_village, ENT_QUOTES, 'UTF-8'),
                    $uploaded_images[0],
                    $uploaded_images[1],
                    $uploaded_images[2],
                    $uploaded_images[3],
                    $uploaded_images[4]
                ]);
            }

            $message = $current_lang === 'gu'
                ? '<div class="alert alert-success fw-bold">🎉 તમારી ચોપડી સફળતાપૂર્વક ઉમેરાઈ ગઈ છે!</div>'
                : '<div class="alert alert-success fw-bold">🎉 Aapki kitab safaltapoorvak list ho gayi hai!</div>';

        } catch (PDOException $e) {

            foreach ($uploaded_images as $uploaded_image) {
                if ($uploaded_image !== null) {
                    $file_path = $upload_dir . $uploaded_image;

                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }

            $message = '<div class="alert alert-danger fw-bold">Database Error: ' .
                htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') .
                '</div>';
        }
    }
}

$villages = [
    'Karnala',
    'Moteta',
    'Khodla',
    'Motahada',
    'Mumanvas',
    'Vasan',
    'Gola',
    'Jalotra',
    'Talepura',
    'Pimpli'
];

$categories = [
    'Standard 1',
    'Standard 2',
    'Standard 3',
    'Standard 4',
    'Standard 5',
    'Standard 6',
    'Standard 7',
    'Standard 8',
    'Standard 9',
    'Standard 10',
    'Standard 11',
    'Standard 12',
    'College',
    'Competitive',
    'General'
];

?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($current_lang) ?>">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= htmlspecialchars($l['page_title']) ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f4f7f6;
            font-family: Arial, sans-serif;
        }

        .sell-card {
            border: 0;
            border-radius: 18px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            min-height: 45px;
        }

        .photo-box {
            background: #f8f9fa;
            border: 1px dashed #198754;
            border-radius: 12px;
        }

        .photo-input {
            margin-bottom: 10px;
        }

        .section-title {
            color: #198754;
            font-weight: 700;
            border-bottom: 2px solid #198754;
            padding-bottom: 8px;
        }

    </style>

</head>

<body>

<nav class="navbar navbar-dark bg-success shadow-sm mb-4">

    <div class="container d-flex justify-content-between align-items-center">

        <a
            class="navbar-brand fw-bold fs-3"
            href="index.php?lang=<?= urlencode($current_lang) ?>"
        >
            <i class="fa-solid fa-book me-2"></i>GaonCart
        </a>

        <div class="btn-group bg-black bg-opacity-25 p-1 rounded-pill">

            <a
                href="?lang=gu"
                class="btn btn-sm rounded-pill px-3 fw-bold <?= $current_lang === 'gu' ? 'btn-warning text-dark' : 'text-white' ?>"
            >
                ગુજરાતી
            </a>

            <a
                href="?lang=en"
                class="btn btn-sm rounded-pill px-3 fw-bold <?= $current_lang === 'en' ? 'btn-warning text-dark' : 'text-white' ?>"
            >
                English
            </a>

        </div>

    </div>

</nav>

<div
    class="container my-3"
    style="max-width: 650px;"
>

    <div class="card shadow sell-card p-4 bg-white">

        <h3 class="fw-bold text-center text-success mb-4">
            <?= $l['page_title'] ?>
        </h3>

        <?= $message ?>

        <form
            action="sell.php?lang=<?= urlencode($current_lang) ?>"
            method="POST"
            enctype="multipart/form-data"
        >

            <h5 class="section-title mb-3">
                <?= $l['sec1_title'] ?>
            </h5>

            <div class="mb-3">

                <label class="form-label fw-bold">
                    <?= $l['book_name'] ?>
                </label>

                <input
                    type="text"
                    name="title"
                    class="form-control"
                    required
                    placeholder="e.g. Class 10 Math NCERT"
                >

            </div>

            <div class="mb-3">

                <label class="form-label fw-bold">
                    <?= $l['author'] ?>
                </label>

                <input
                    type="text"
                    name="author"
                    class="form-control"
                    placeholder="e.g. RD Sharma"
                >

            </div>

            <div class="row">

                <div class="col-6 mb-3">

                    <label class="form-label fw-bold">
                        <?= $l['price'] ?>
                    </label>

                    <input
                        type="number"
                        name="price"
                        class="form-control"
                        min="1"
                        step="0.01"
                        required
                        placeholder="e.g. 100"
                    >

                    <small class="text-muted">
                        <?= $l['price_note'] ?>
                    </small>

                </div>

                <div class="col-6 mb-3">

                    <label class="form-label fw-bold">
                        <?= $l['condition'] ?>
                    </label>

                    <select
                        name="condition"
                        class="form-select"
                        required
                    >

                        <option value="Theek Thaak">
                            <?= $l['cond_good'] ?>
                        </option>

                        <option value="Bilkul Nayi Jaisi">
                            <?= $l['cond_new'] ?>
                        </option>

                        <option value="Purani (Thodi Fati)">
                            <?= $l['cond_old'] ?>
                        </option>

                    </select>

                </div>

            </div>

            <div class="mb-3">

                <label class="form-label fw-bold">
                    <?= $l['all_villages'] ?>
                </label>

                <select
                    name="village"
                    class="form-select"
                    required
                >

                    <option value="">
                        <?= $l['all_villages'] ?>
                    </option>

                    <?php foreach ($villages as $v): ?>

                        <option value="<?= htmlspecialchars($v) ?>">
                            <?= htmlspecialchars($v) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label fw-bold">
                    <?= $l['category'] ?>
                </label>

                <select
                    name="book_category"
                    class="form-select"
                    required
                >

                    <option value="" disabled selected>
                        Select Category / Class
                    </option>

                    <?php foreach ($categories as $category): ?>

                        <option value="<?= htmlspecialchars($category) ?>">
                            <?= htmlspecialchars($category === 'General' ? 'Story Book' : $category) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="mb-3 p-3 photo-box">

                <label class="form-label fw-bold text-success">
                    <i class="fa-solid fa-images me-1"></i>
                    <?= $l['photos'] ?>
                </label>

                <input
                    type="file"
                    name="image1"
                    class="form-control photo-input"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <input
                    type="file"
                    name="image2"
                    class="form-control photo-input"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <input
                    type="file"
                    name="image3"
                    class="form-control photo-input"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <input
                    type="file"
                    name="image4"
                    class="form-control photo-input"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <input
                    type="file"
                    name="image5"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <small class="text-muted d-block mt-2">
                    Maximum 5 images. Each image must be 5MB or smaller.
                </small>

            </div>

            <div class="mb-3">

                <label class="form-label fw-bold">
                    <?= $l['desc'] ?>
                </label>

                <textarea
                    name="description"
                    class="form-control"
                    rows="3"
                    placeholder="<?= htmlspecialchars($l['desc_ph']) ?>"
                ></textarea>

            </div>

            <h5 class="section-title mb-3 mt-4">
                <?= $l['sec2_title'] ?>
            </h5>

            <p class="text-muted small">
                <?= $l['sec2_note'] ?>
            </p>

            <div class="mb-3">

                <label class="form-label fw-bold">
                    <?= $l['seller_name'] ?>
                </label>

                <input
                    type="text"
                    name="seller_name"
                    class="form-control"
                    value="<?= htmlspecialchars($default_name) ?>"
                    required
                    placeholder="Apna naam / નામ લખો"
                >

            </div>

            <div class="mb-3">

                <label class="form-label fw-bold">
                    <?= $l['seller_phone'] ?>
                </label>

                <input
                    type="tel"
                    name="seller_phone"
                    class="form-control"
                    value="<?= htmlspecialchars($default_phone) ?>"
                    pattern="[0-9]{10}"
                    maxlength="10"
                    required
                    placeholder="Phone / મોબાઈલ નંબર"
                >

            </div>

            <div class="mb-3">

                <label class="form-label fw-bold">
                    <?= $l['seller_village'] ?>
                </label>

                <select
                    name="seller_village"
                    class="form-select"
                    required
                >

                    <option value="">
                        <?= $l['seller_village'] ?>
                    </option>

                    <?php foreach ($villages as $v): ?>

                        <option
                            value="<?= htmlspecialchars($v) ?>"
                            <?= $default_village === $v ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($v) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <button
                type="submit"
                class="btn btn-success w-100 fw-bold py-2 mt-3 fs-5"
            >
                <?= $l['submit_btn'] ?>
            </button>

            <a
                href="index.php?lang=<?= urlencode($current_lang) ?>"
                class="btn btn-link w-100 text-center mt-2 text-decoration-none text-muted"
            >
                <?= $l['back_home'] ?>
            </a>

        </form>

    </div>

</div>

</body>
</html>