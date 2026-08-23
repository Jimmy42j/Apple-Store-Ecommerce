<?php
// payment.php
session_start();
require_once 'dbconn.php';

// 1) Must be logged in
if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// 2) Must have ?order_id=…
if (empty($_GET['order_id'])) {
  header('Location: checkout.php');
  exit;
}
$order_id = (int)$_GET['order_id'];
$user_id  = $_SESSION['user_id'];

// 3) Verify order belongs to this user & get total
$stmt = $conn->prepare("
  SELECT total_amount
    FROM orders
   WHERE order_id = ? AND user_id = ?
");
$stmt->execute([$order_id, $user_id]);
$total = $stmt->fetchColumn();
if ($total === false) {
  die("Order #{$order_id} not found or you don’t have permission to pay it.");
}

// ✅ FIXED QUERY: Join by product_id and accessory_id separately
$lineStmt = $conn->prepare("
  SELECT 
    od.quantity,
    od.unit_price,
    od.total_price,
    COALESCE(p.name, a.item_name) AS item_name,
    COALESCE(p.image, a.image_path) AS image
  FROM order_detail od
  LEFT JOIN products p ON od.product_id = p.product_id
  LEFT JOIN accessories a ON od.accessory_id = a.item_id
  WHERE od.order_id = ?
");
$lineStmt->execute([$order_id]);
$lines = $lineStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Secure Payment — Order #<?= $order_id ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box }
    body {
      margin: 0;
      padding: 40px;
      font-family: 'Inter', sans-serif;
      background: #f8f9fb;
    }

    .container {
      max-width: 900px;
      margin: auto;
      display: flex;
      flex-wrap: wrap;
      gap: 40px;
      background: #fff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .left, .right {
      flex: 1 1 300px;
    }

    h1 {
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 25px;
    }

    .order-total {
      font-size: 36px;
      font-weight: 700;
      color: #111;
      margin-bottom: 20px;
    }

    .line-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #eee;
      padding: 15px 0;
    }

    .line-item img {
      width: 48px;
      height: 48px;
      border-radius: 8px;
      object-fit: cover;
      margin-right: 15px;
    }

    .item-info {
      flex-grow: 1;
      display: flex;
      align-items: center;
    }

    .name {
      font-weight: 500;
      margin-bottom: 4px;
    }

    .qty {
      font-size: 13px;
      color: #666;
    }

    .amount {
      font-weight: 600;
    }

    form label {
      display: block;
      font-size: 14px;
      margin: 10px 0 6px;
    }

    form input {
      width: 100%;
      padding: 12px;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-bottom: 15px;
    }

    .split {
      display: flex;
      gap: 12px;
    }

    .split .col {
      flex: 1;
    }

    button {
      width: 100%;
      padding: 14px;
      background-color: #111;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    button:hover {
      background: #222;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }
    }

    .form-group {
  margin-bottom: 16px;
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-size: 14px;
  margin-bottom: 6px;
  color: #333;
}

.form-group input {
  padding: 12px 14px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 6px;
  transition: border-color 0.2s;
}

.form-group input:focus {
  border-color: #0070f3;
  outline: none;
}

.form-row {
  display: flex;
  gap: 12px;
}

.submit-button {
  width: 100%;
  background-color: #000;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 14px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 20px;
  transition: background-color 0.2s;
}

.submit-button:hover {
  background-color: #111;
}
.payment-options {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  margin-bottom: 20px;
  font-size: 15px;
}

.payment-options label {
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
}

.card-payment, .wallet-info {
  margin-top: 10px;
}


  </style>
  <script>
  
  window.onload = function () {
  const radios = document.querySelectorAll('input[name="payment_method"]');
  const cardSection = document.querySelector('.card-payment');
  const walletSection = document.querySelector('.wallet-info');
  const codSection = document.querySelector('.cod-address'); // NEW

  function updateVisibility() {
    const selected = document.querySelector('input[name="payment_method"]:checked').value;

    cardSection.style.display = (selected === 'card') ? 'block' : 'none';
    walletSection.style.display = (selected === 'kpay' || selected === 'wavepay') ? 'block' : 'none';
    codSection.style.display = (selected === 'cod') ? 'block' : 'none'; // NEW
  }

  radios.forEach(radio => {
    radio.addEventListener('change', updateVisibility);
  });

  updateVisibility();
};



</script>

</head>
<body>

<div class="container">
  <!-- LEFT: ORDER DETAILS -->
  <div class="left">
    <h1>Order #<?= $order_id ?></h1>
    <div class="order-total">$<?= number_format($total, 2) ?></div>

    <?php foreach ($lines as $ln): ?>
      <div class="line-item">
        <div class="item-info">
          <img src="<?= htmlspecialchars($ln['image']) ?>" alt="">
          <div>
            <div class="name"><?= htmlspecialchars($ln['item_name']) ?></div>
            <div class="qty">Qty × <?= (int)$ln['quantity'] ?></div>
          </div>
        </div>
        <div class="amount"><?= number_format($ln['total_price'], 2) ?> $</div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- RIGHT: PAYMENT FORM -->
  <div class="right">
    <form method="POST" action="process_payment.php">
  <input type="hidden" name="order_id" value="<?= $order_id ?>">

  <!-- Payment method selection -->
  <div class="form-group">
    <label>Choose Payment Method</label>
    <div class="payment-options">
      <label><input type="radio" name="payment_method" value="card" checked> Credit / Debit Card</label>
      <label><input type="radio" name="payment_method" value="cod"> Cash on Delivery</label>
      <label><input type="radio" name="payment_method" value="kpay"> KBZPay</label>
      <label><input type="radio" name="payment_method" value="wavepay"> WavePay</label>
      <label><input type="radio" name="payment_method" value="paypal"> PayPal</label>
    </div>
  </div>

  <!-- Card payment section -->
  <div class="card-payment">
    <div class="form-group">
      <label for="card_number">Card Number</label>
      <input type="tel" name="card_number" id="card_number" placeholder="1234 5678 9012 3456">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="expiry">Expiry (MM/YY)</label>
        <input type="text" name="expiry" id="expiry" placeholder="MM/YY">
      </div>
      <div class="form-group">
        <label for="cvc">CVC</label>
        <input type="text" name="cvc" id="cvc" placeholder="123">
      </div>
    </div>
  </div>

  <!-- Optional wallet info -->
  <div class="wallet-info" style="display:none;">
    <div class="form-group">
      <label for="wallet_phone">Wallet Phone Number</label>
      <input type="tel" name="wallet_phone" id="wallet_phone" placeholder="09xxxxxxx">
    </div>
  </div>

  <div class="cod-address" style="display:none;">
  <div class="form-group">
    <label for="address_line1">Address Line 1</label>
    <input type="text" name="address_line1" id="address_line1" placeholder="Street, Building, etc.">
  </div>
  <div class="form-group">
    <label for="address_city">City</label>
    <input type="text" name="address_city" id="address_city" placeholder="City">
  </div>
  <div class="form-group">
    <label for="address_zip">Postal Code / ZIP</label>
    <input type="text" name="address_zip" id="address_zip" placeholder="ZIP code">
  </div>
</div>

  <button type="submit" class="submit-button">Pay Now</button>



</form>



</body>
</html>
