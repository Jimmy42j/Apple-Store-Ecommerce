<?php
session_start();
require_once "dbconn.php";

if (empty($_SESSION['user_id']) || empty($_GET['order_id'])) {
    die("Invalid order.");
}
$user_id  = $_SESSION['user_id'];
$order_id = (int)$_GET['order_id'];

// 1) Fetch the order
$stmt = $conn->prepare("
    SELECT * 
      FROM orders 
     WHERE order_id = ? 
       AND user_id  = ?
");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) {
    die("Order not found.");
}

// ✅ 2) Join both products and accessories using COALESCE
$q = $conn->prepare("
    SELECT od.quantity,
           od.unit_price,
           od.total_price,
           COALESCE(p.name, a.item_name) AS name
      FROM order_detail od
 LEFT JOIN products p ON od.product_id = p.product_id
 LEFT JOIN accessories a ON od.accessory_id = a.item_id
     WHERE od.order_id = ?
");
$q->execute([$order_id]);
$details = $q->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order #<?= $order_id ?> Confirmed</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter',sans-serif; padding:40px; background:#f5f5f5; }
    .card { background:#fff; padding:30px; border-radius:8px; max-width:600px; margin:auto; }
    h1 { margin-top:0; }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th, td { padding:8px; border-bottom:1px solid #eee; text-align:left; }
    th { font-weight:600; }
    .total { text-align:right; margin-top:20px; font-size:18px; font-weight:600; }
    a.button { display:inline-block; margin-top:30px; padding:12px 24px; background:#007acc; color:#fff; text-decoration:none; border-radius:5px; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Thank you for your order!</h1>
    <p>Your order <strong>#<?= $order_id ?></strong> was placed on <?= $order['created_at'] ?>.</p>

    <table>
      <tr>
        <th>Product</th>
        <th>Qty</th>
        <th>Line Total</th>
      </tr>
      <?php foreach($details as $d): ?>
      <tr>
        <td><?= htmlspecialchars($d['name']) ?></td>
        <td><?= $d['quantity'] ?></td>
        <td><?= number_format($d['total_price'],2) ?> $</td>
      </tr>
      <?php endforeach; ?>
    </table>

    <div class="total">
      Grand Total: <?= number_format($order['total_amount'],2) ?> $
    </div>

    <a class="button" href="index.html">Continue Shopping</a>
  </div>
</body>
</html>
