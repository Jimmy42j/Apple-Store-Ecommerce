<?php
session_start();
require_once "dbconn.php";
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
// 1) grab cart & items
$stmt = $conn->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_id = $stmt->fetchColumn();
if (!$cart_id) {
    header("Location: cart.php");
    exit;
}

$q = $conn->prepare("
  SELECT product_id, quantity, unit_price, total_price
    FROM cart_items
   WHERE cart_id = ?
");
$q->execute([$cart_id]);
$items = $q->fetchAll(PDO::FETCH_ASSOC);
if (!$items) {
    header("Location: cart.php");
    exit;
}

// 2) compute grand total
$grand = 0;
foreach ($items as $i) $grand += $i['total_price'];

// 3) insert order
$o = $conn->prepare("
  INSERT INTO orders (user_id, total_amount)
        VALUES (?, ?)
");
$o->execute([$user_id, $grand]);
$order_id = $conn->lastInsertId();

// 4) insert order_details
$ins = $conn->prepare("
  INSERT INTO order_details
    (order_id,product_id,quantity,unit_price,total_price)
  VALUES (?,?,?,?,?)
");
foreach ($items as $i) {
  $ins->execute([
    $order_id, 
    $i['product_id'], 
    $i['quantity'], 
    $i['unit_price'], 
    $i['total_price']
  ]);
}

// 5) clear cart
$conn->prepare("DELETE FROM cart_items WHERE cart_id = ?")
     ->execute([$cart_id]);

// optional: delete cart header
$conn->prepare("DELETE FROM carts WHERE cart_id = ?")
     ->execute([$cart_id]);

// done
header("Location: thank_you.php?order_id={$order_id}");
exit;
