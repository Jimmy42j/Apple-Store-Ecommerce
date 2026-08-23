<?php
session_start();
require_once "dbconn.php";

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_id = $stmt->fetchColumn();

$items    = [];
$subtotal = 0;
if ($cart_id) {
    $q = $conn->prepare("
      SELECT 
        ci.cart_item_id, ci.quantity, ci.unit_price, ci.total_price,
        p.name AS product_name, p.image,
        a.item_name AS accessory_name, a.image_path
      FROM cart_items ci
      LEFT JOIN products p ON ci.product_id = p.product_id
      LEFT JOIN accessories a ON ci.accessory_id = a.item_id
      WHERE ci.cart_id = ?
    ");
    $q->execute([$cart_id]);
    $items = $q->fetchAll(PDO::FETCH_ASSOC);
    foreach ($items as $i) {
      $subtotal += $i['total_price'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: #fafafa;
      color: #111;
      padding: 40px 60px;
    }

    h1 {
      font-size: 28px;
      margin-bottom: 20px;
    }

    .empty-cart {
      font-size: 16px;
      color: #555;
    }

    .empty-cart a {
      color: #6c2bd9;
      text-decoration: underline;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      border-radius: 10px;
      overflow: hidden;
    }

    th, td {
      padding: 18px;
      border-bottom: 1px solid #eee;
      text-align: left;
      font-size: 15px;
    }

    th {
      background-color: #f8f8f8;
      font-weight: 600;
    }

    .product-info {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .product-info img {
      width: 60px;
      height: 60px;
      border-radius: 10px;
      object-fit: cover;
    }

    .qty {
      width: 60px;
      padding: 6px 10px;
      text-align: center;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .actions a {
      color: #c00;
      text-decoration: none;
      font-size: 14px;
    }

    .totals {
      text-align: right;
      margin-top: 20px;
      font-size: 18px;
      font-weight: 600;
    }

    #checkout {
      margin-top: 20px;
      padding: 14px 30px;
      background: #000;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      cursor: pointer;
      transition: background 0.3s;
    }

    #checkout:hover {
      background: #333;
    }

    @media (max-width: 768px) {
      body {
        padding: 20px;
      }
      .product-info {
        flex-direction: column;
        align-items: flex-start;
      }
    }

    .empty-container {
      height: 60vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }

    .empty-container h1 {
      font-size: 30px;
      margin-bottom: 10px;
    }

    .empty-container p {
      font-size: 16px;
      color: #555;
      margin-bottom: 20px;
    }

    .shop-btn {
      background-color: #000;
      color: white;
      padding: 12px 28px;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .shop-btn:hover {
      background-color: #333;
      transform: translateY(-1px);
    }
  </style>
</head>
<body>

<?php if (empty($items)): ?>
  <div class="empty-container">
    <h1>Your Cart is Empty</h1>
    <p>Looks like you haven't added anything yet.</p>
    <a href="shop.html" class="shop-btn">Shop Now</a>
  </div>
<?php else: ?>

  <form id="cart-form" action="update_cart.php" method="POST">
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Quantity</th>
          <th>Total</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($items as $i): ?>
        <?php
          $name = $i['product_name'] ?? $i['accessory_name'];
          $img = $i['image'] ?? $i['image_path'];
        ?>
        <tr>
          <td>
            <div class="product-info">
              <img src="<?= htmlspecialchars($img) ?>" alt="product">
              <?= htmlspecialchars($name) ?>
            </div>
          </td>
          <td>
            <input
              type="number"
              name="qty[<?= $i['cart_item_id'] ?>]"
              value="<?= $i['quantity'] ?>"
              min="1"
              class="qty"
              onchange="document.getElementById('cart-form').submit()"
            >
          </td>
          <td><?= number_format($i['total_price'], 0) ?> $</td>
          <td class="actions">
            <a href="remove_from_cart.php?id=<?= $i['cart_item_id'] ?>">Remove</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </form>

  <div class="totals">
    Subtotal: <?= number_format($subtotal, 0) ?> $
  </div>

  <form action="checkout.php" method="POST">
    <button type="submit" id="checkout">Checkout</button>
  </form>

<?php endif; ?>
</body>
</html>
