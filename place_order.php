<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $book_id      = intval($_POST['book_id']);
    $final_price  = floatval($_POST['final_price']);
    $cust_name    = htmlspecialchars(trim($_POST['cust_name']));
    $cust_phone   = htmlspecialchars(trim($_POST['cust_phone']));
    $customer_email =htmlspecialchars(trim($_POST['cust_email']));
    $cust_village = htmlspecialchars($_POST['cust_village']);
    $cust_address = htmlspecialchars(trim($_POST['cust_address']));
    $order_status = 'Pending';

    if (!empty($book_id) && !empty($cust_name) && !empty($cust_phone) && !empty($customer_email) && !empty($cust_address)) {
        
        $stmt = $pdo->prepare("INSERT INTO orders (book_id, customer_name, customer_phone, customer_email, customer_village, delivery_address, final_price, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$book_id, $cust_name, $cust_phone, $customer_email, $cust_village, $cust_address, $final_price, $order_status])) {
            $order_id = $pdo->lastInsertId();

            $_SESSION['last_customer_phone'] = $cust_phone;
            $_SESSION['last_order_id']       = $order_id;

            header("Location: my_orders.php?status=success&order_id=" . $order_id);
            exit();
        } else {
            header("Location: book-details.php?id=" . $book_id . "&error=1");
            exit();
        }
    } else {
        header("Location: book-details.php?id=" . $book_id . "&error=1");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>