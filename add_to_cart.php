<?php
session_start();
require_once "dbconn.php";

// 1) User must be logged in
if (!isset($_SESSION['user_id'])) {
  die("You must be logged in to add items to your cart.");
}
$user_id = (int)$_SESSION['user_id'];

// 2) Accept either product_id or accessory_id
$product_id    = isset($_POST['product_id'])    ? (int)$_POST['product_id']    : null;
$accessory_id  = isset($_POST['accessory_id'])  ? (int)$_POST['accessory_id']  : null;
$qty           = isset($_POST['quantity'])      ? max(1, intval($_POST['quantity'])) : 0;

if ((!$product_id && !$accessory_id) || $qty < 1) {
  die("Invalid item or quantity.");
}

// 3) Get or create user's cart
$stmt = $conn->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_id = $stmt->fetchColumn();

if (!$cart_id) {
  $stmt = $conn->prepare("INSERT INTO carts (user_id, created_at) VALUES (?, NOW())");
  $stmt->execute([$user_id]);
  $cart_id = $conn->lastInsertId();
}

// 4) Check if item already exists
if ($product_id) {
  $ci = $conn->prepare("SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
  $ci->execute([$cart_id, $product_id]);
} else {
  $ci = $conn->prepare("SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = ? AND accessory_id = ?");
  $ci->execute([$cart_id, $accessory_id]);
}

$existing = $ci->fetch(PDO::FETCH_ASSOC);

// 5) Get unit price
if ($product_id) {
  $priceStmt = $conn->prepare("SELECT price FROM products WHERE product_id = ?");
  $priceStmt->execute([$product_id]);
} else {
  $priceStmt = $conn->prepare("SELECT price FROM accessories WHERE item_id = ?");
  $priceStmt->execute([$accessory_id]);
}
$unit = $priceStmt->fetchColumn();

if (!$unit) {
  die("Item not found.");
}

// 6) Insert or update cart item
if ($existing) {
  $newQty = $existing['quantity'] + $qty;
  $upd = $conn->prepare("
    UPDATE cart_items
       SET quantity = ?, total_price = unit_price * ?
     WHERE cart_item_id = ?
  ");
  $upd->execute([$newQty, $newQty, $existing['cart_item_id']]);
} else {
  $ins = $conn->prepare("
    INSERT INTO cart_items
      (cart_id, product_id, accessory_id, quantity, unit_price, total_price)
    VALUES (?, ?, ?, ?, ?, ?)
  ");
  $ins->execute([$cart_id, $product_id, $accessory_id, $qty, $unit, $unit * $qty]);
}

// 7) Redirect back
$referer = $_SERVER['HTTP_REFERER'] ?? "index.php";
header("Location: {$referer}");
exit;
