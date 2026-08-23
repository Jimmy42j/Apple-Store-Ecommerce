<?php
session_start();
require_once "dbconn.php";

// Must be logged in
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch cart
$stmt = $conn->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_id = $stmt->fetchColumn();

if (!$cart_id) {
    die("Cart not found.");
}

// Fetch cart items (both products and accessories)
$q = $conn->prepare("
  SELECT 
    ci.cart_item_id,
    ci.quantity,
    ci.unit_price,
    ci.total_price,
    ci.product_id,
    ci.accessory_id,
    COALESCE(p.name, a.item_name) AS name,
    COALESCE(p.image, a.image_path) AS image
  FROM cart_items ci
  LEFT JOIN products p ON ci.product_id = p.product_id
  LEFT JOIN accessories a ON ci.accessory_id = a.item_id
  WHERE ci.cart_id = ?
");
$q->execute([$cart_id]);
$items = $q->fetchAll(PDO::FETCH_ASSOC);

if (!$items) {
    die("Cart is empty.");
}

// Create order
$total = array_sum(array_column($items, 'total_price'));
$ins = $conn->prepare("INSERT INTO orders (user_id, total_amount, created_at) VALUES (?, ?, NOW())");
$ins->execute([$user_id, $total]);
$order_id = $conn->lastInsertId();

// Insert each cart item into order_detail
$detail = $conn->prepare("
  INSERT INTO order_detail 
    (order_id, product_id, accessory_id, quantity, unit_price, total_price)
  VALUES (?, ?, ?, ?, ?, ?)
");
foreach ($items as $i) {
    $detail->execute([
        $order_id,
        $i['product_id'] ?: null,
        $i['accessory_id'] ?: null,
        $i['quantity'],
        $i['unit_price'],
        $i['total_price']
    ]);
}

// Clear cart
$conn->prepare("DELETE FROM cart_items WHERE cart_id = ?")->execute([$cart_id]);

// Redirect to payment
header("Location: payment.php?order_id={$order_id}");
exit;
?>
