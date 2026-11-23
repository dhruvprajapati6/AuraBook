<?php
include 'db.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure Password
    $village = htmlspecialchars($_POST['village']);

    if (!empty($name) && !empty($email) && !empty($phone) && !empty($_POST['password'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, village_name) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $password, $village]);
            $message = "<div class='alert alert-success'>Registration safal raha! <a href='login.php'>Yahan se Login karein</a></div>";
        } catch (\PDOException $e) {
            $message = "<div class='alert alert-danger'>Email pehle se register hai!</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Sari jankari bharna zaroori hai.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naya Account Banayein - AuraBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 500px;">
    <div class="card shadow border-0 p-4">
        <h3 class="fw-bold text-center text-success mb-4">Naya Account Banayein</h3>
        <?= $message; ?>
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Aapka Naam</label>
                <input type="text" name="name" class="form-control" required placeholder="Name dalein">
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="Email dalein">
            </div>
            <div class="mb-3">
                <label class="form-label">Mobile Number</label>
                <input type="text" name="phone" class="form-control" required placeholder="Phone number dalein">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Ek mazboot password dalein">
            </div>
            <div class="mb-4">
                <label class="form-label">Aapka Gaon (Village)</label>
                <select name="village" class="form-select" required>
                    <option value="Rampur">Rampur</option>
                    <option value="Chandanpur">Chandanpur</option>
                    <option value="Gopalpur">Gopalpur</option>
                    <option value="Sultanpur">Sultanpur</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success w-100 fw-bold py-2">Register Karein</button>
            <p class="text-center mt-3 small">Pehle se account hai? <a href="login.php" class="text-success fw-bold text-decoration-none">Login Karein</a></p>
        </form>
    </div>
</div>
</body>
</html>