<?php
include 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$error = '';
$success = '';

// ---------------------------------------------------------
// 1. PDF UPLOAD LOGIC
// ---------------------------------------------------------
if (isset($_POST['upload_pdf'])) {
    if (!$user_id) {
        $error = "❌ PDF Upload karne ke liye pehle Login karein.";
    } else {
        $title       = trim($_POST['title']);
        $author      = trim($_POST['author']);
        $category    = $_POST['category'];
        $description = trim($_POST['description']);

        // Directories Setup
        $pdf_dir = 'uploads/pdfs/';
        $cover_dir = 'uploads/pdf_covers/';

        if (!file_exists($pdf_dir)) mkdir($pdf_dir, 0777, true);
        if (!file_exists($cover_dir)) mkdir($cover_dir, 0777, true);

        // PDF File Upload Check
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
            $pdf_ext = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));
            
            if ($pdf_ext !== 'pdf') {
                $error = "❌ Sirf PDF Format file upload karein.";
            } elseif ($_FILES['pdf_file']['size'] > 20 * 1024 * 1024) { // Max 20MB
                $error = "❌ File size 20MB se kam hona chahiye.";
            } else {
                $pdf_filename = "pdf_" . time() . "_" . rand(1000, 9999) . ".pdf";
                move_uploaded_file($_FILES['pdf_file']['tmp_name'], $pdf_dir . $pdf_filename);

                // Optional Cover Image Upload
                $cover_filename = 'default_cover.jpg';
                if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
                    $img_ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
                    if (in_array($img_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $cover_filename = "cover_" . time() . "_" . rand(1000, 9999) . "." . $img_ext;
                        move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_dir . $cover_filename);
                    }
                }

                // Database Entry
                $stmt = $pdo->prepare("INSERT INTO pdf_books (user_id, title, author, category, description, pdf_file, cover_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$user_id, $title, $author, $category, $description, $pdf_filename, $cover_filename])) {
                    header("Location: thankyouold.html");                } 
                    else {
                    $error = "❌ Database me add karte waqt error aaya.";
                }
            }
        } else {
            $error = "❌ Kripya valid PDF file select karein.";
        }
    }
}

// ---------------------------------------------------------
// 2. DOWNLOAD COUNTER INCREMENT LOGIC
// ---------------------------------------------------------
if (isset($_GET['download_id'])) {
    $download_id = (int)$_GET['download_id'];
    
    // Counter Update
    $update_stmt = $pdo->prepare("UPDATE pdf_books SET downloads = downloads + 1 WHERE id = ?");
    $update_stmt->execute([$download_id]);

    // File Fetch
    $fetch_stmt = $pdo->prepare("SELECT pdf_file, title FROM pdf_books WHERE id = ?");
    $fetch_stmt->execute([$download_id]);
    $pdf = $fetch_stmt->fetch(PDO::FETCH_ASSOC);

    if ($pdf && file_exists('uploads/pdfs/' . $pdf['pdf_file'])) {
        $file_path = 'uploads/pdfs/' . $pdf['pdf_file'];
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($pdf['title']) . '.pdf"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit();
    }
}

// ---------------------------------------------------------
// 3. FETCH & FILTER PDFS
// ---------------------------------------------------------
$search = trim($_GET['search'] ?? '');
$cat_filter = $_GET['category'] ?? '';

$query = "SELECT * FROM pdf_books WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($cat_filter)) {
    $query .= " AND category = ?";
    $params[] = $cat_filter;
}

$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$pdf_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF E-Library - KitabBazar</title>
    
    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { background-color: #f4f7f6; font-family: 'Plus Jakarta Sans', sans-serif; color: #2d3748; }
        .pdf-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .pdf-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .cover-wrapper {
            height: 220px;
            background-color: #2b3035;
            position: relative;
            overflow: hidden;
        }
        .cover-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .pdf-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: #fff;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 6px;
        }
        .pdf-viewer-frame {
            width: 100%;
            height: 80vh;
            border: none;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-success shadow-sm sticky-top">
    <div class="container d-flex justify-content-between">
        <a class="navbar-brand fw-bold fs-4" href="index.php"> <i class="fa-solid fa-book"></i>&nbsp;&nbsp;GaonCart E-Library</a>
        <div>
            <button class="btn btn-sm btn-light fw-bold text-success me-2" data-bs-toggle="modal" data-bs-target="#uploadPdfModal">
                <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload PDF
            </button>
            <a href="index.php" class="btn btn-sm btn-outline-light fw-bold">Dashboard</a>
        </div>
    </div>
</nav>

<div class="container my-4">

    <!-- ALERT MESSAGES -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger rounded-3 fw-bold mb-4"><?= $error ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success rounded-3 fw-bold mb-4"><?= $success ?></div>
    <?php endif; ?>

    <!-- HEADER & SEARCH -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6 mb-3 mb-md-0">
            <h3 class="fw-bold text-dark mb-0">📄 Free Digital PDF Books</h3>
            <p class="text-muted small mb-0">PDF Kitabein Online Padhein ya Direct Download Karein</p>
        </div>
        <div class="col-md-6">
            <form method="GET" action="pdf_library.php" class="row g-2">
                <div class="col-7">
                    <input type="text" name="search" class="form-control rounded-3" placeholder="Book ya Author search karein..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-5 d-flex gap-2">
                    <select name="category" class="form-select rounded-3">
                        <option value="">Sabhi Categories</option>
                        <option value="Education" <?= $cat_filter == 'Education' ? 'selected' : '' ?>>Education</option>
                        <option value="Competitive Exam" <?= $cat_filter == 'Competitive Exam' ? 'selected' : '' ?>>Competitive Exam</option>
                        <option value="Story Books" <?= $cat_filter == 'Story Books' ? 'selected' : '' ?>>Story Books</option>
                        <option value="Other" <?= $cat_filter == 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                    <button type="submit" class="btn btn-success fw-bold"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- PDF GRID -->
    <div class="row g-4">
        <?php if (count($pdf_list) > 0): ?>
            <?php foreach ($pdf_list as $pdf): ?>
                <?php 
                    $cover_src = !empty($pdf['cover_image']) && file_exists('uploads/pdf_covers/' . $pdf['cover_image']) && $pdf['cover_image'] != 'default_cover.jpg'
                        ? 'uploads/pdf_covers/' . $pdf['cover_image'] 
                        : 'https://via.placeholder.com/300x400/198754/FFFFFF?text=PDF+Book';
                    $pdf_file_path = 'uploads/pdfs/' . $pdf['pdf_file'];
                ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card pdf-card h-100">
                        
                        <div class="cover-wrapper">
                            <img src="<?= htmlspecialchars($cover_src) ?>" alt="Book Cover">
                            <span class="pdf-badge"><i class="fa-solid fa-file-pdf me-1"></i>PDF</span>
                        </div>

                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-light text-success border mb-1" style="font-size: 0.65rem;"><?= htmlspecialchars($pdf['category']) ?></span>
                                <h6 class="fw-bold text-dark text-truncate mb-1" title="<?= htmlspecialchars($pdf['title']) ?>"><?= htmlspecialchars($pdf['title']) ?></h6>
                                <p class="text-muted small mb-2 text-truncate">By <?= htmlspecialchars($pdf['author'] ?: 'Unknown') ?></p>
                            </div>

                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                                    <span><i class="fa-solid fa-download me-1 text-primary"></i> <?= $pdf['downloads'] ?> Downloads</span>
                                </div>

                                <div class="d-grid gap-2">
                                    <!-- Read Online Button -->
                                    <button class="btn btn-outline-success btn-sm fw-bold rounded-3" onclick="openPdfReader('<?= htmlspecialchars(addslashes($pdf['title'])) ?>', '<?= $pdf_file_path ?>')">
                                        <i class="fa-solid fa-book-open me-1"></i> Read Online
                                    </button>

                                    <!-- Direct Download Button -->
                                    <a href="pdf_library.php?download_id=<?= $pdf['id'] ?>" class="btn btn-success btn-sm fw-bold rounded-3">
                                        <i class="fa-solid fa-download me-1"></i> Download
                                    </a>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-file-pdf fs-1 text-muted mb-3"></i>
                <h5 class="fw-bold text-muted">Koi PDF Book Nahi Mili!</h5>
                <p class="text-muted small">Pehle aap apni PDF upload karein.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- UPLOAD PDF MODAL -->
<div class="modal fade" id="uploadPdfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            
            <div class="modal-header bg-success text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload PDF Book</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <form action="pdf_library.php" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">PDF Title *</label>
                        <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Science Class 10 Notes" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Author / Publisher</label>
                            <input type="text" name="author" class="form-control rounded-3" placeholder="e.g. NCERT">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Category *</label>
                            <select name="category" class="form-select rounded-3" required>
                                <option value="Education">Education</option>
                                <option value="Competitive Exam">Competitive Exam</option>
                                <option value="Story Books">Story Books</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Select PDF File (Max 20MB) *</label>
                        <input type="file" name="pdf_file" accept=".pdf" class="form-control rounded-3" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Cover Image (Optional)</label>
                        <input type="file" name="cover_image" accept="image/*" class="form-control rounded-3">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Description (Optional)</label>
                        <textarea name="description" class="form-control rounded-3" rows="2" placeholder="Brief details about PDF..."></textarea>
                    </div>

                    <button type="submit" name="upload_pdf" class="btn btn-success w-100 fw-bold py-2 rounded-3">
                        Upload PDF Now
                    </button>

                </form>
            </div>

        </div>
    </div>
</div>

<!-- IN-BROWSER PDF READER MODAL -->
<div class="modal fade" id="pdfReaderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            
            <div class="modal-header bg-dark text-white border-0 py-2 px-3 d-flex justify-content-between align-items-center">
                <h6 class="modal-title fw-bold text-truncate" id="readerTitle"><i class="fa-solid fa-book-open text-success me-2"></i>PDF Reader</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0 bg-secondary">
                <iframe id="pdfIframe" class="pdf-viewer-frame" src=""></iframe>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const readerModal = new bootstrap.Modal(document.getElementById('pdfReaderModal'));

function openPdfReader(title, pdfPath) {
    document.getElementById('readerTitle').innerText = title;
    // Set iframe source to the PDF path
    document.getElementById('pdfIframe').src = pdfPath + '#toolbar=1&navpanes=0';
    readerModal.show();
}

// Clear iframe source on close to stop loading in background
document.getElementById('pdfReaderModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('pdfIframe').src = '';
});
</script>

</body>
</html>