<?php
// process_payment.php
session_start();
require_once 'dbconn.php';

// 1) Must be logged in
if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// 2) Retrieve & validate POSTed data
$order_id       = isset($_POST['order_id'])       ? (int)$_POST['order_id'] : 0;
$payment_method = trim($_POST['payment_method']   ?? '');
$card_number    = trim($_POST['card_number']      ?? '');
$expiry         = trim($_POST['expiry']           ?? '');
$cvc            = trim($_POST['cvc']              ?? '');
$wallet_phone   = trim($_POST['wallet_phone']     ?? '');

if (!$order_id || !$payment_method) {
  die("Missing order or payment method.");
}

// 3) Verify order belongs to user & is unpaid
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT total_amount, status FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
  die("Order not found or not yours.");
}
if ($order['status'] !== 'pending') {
  die("This order cannot be paid (current status: {$order['status']}).");
}

// 4) Process payment based on selected method
$success = false;

switch ($payment_method) {
  case 'card':
    if (!$card_number || !$expiry || !$cvc) {
      die("Missing card information.");
    }
    // In real-world, call Stripe/PayPal API here
    $success = true;
    break;

  case 'kpay':
  case 'wavepay':
    if (!$wallet_phone) {
      die("Please enter your wallet phone number.");
    }
    // Simulate wallet transfer success
    $success = true;
    break;

  case 'cod':
    // Always allow cash on delivery
    $success = true;
    break;

  case 'paypal':
    // In real-world, redirect to PayPal checkout
    $success = true;
    break;

  default:
    die("Invalid payment method.");
}

if (!$success) {
  die("Payment failed. Please try again.");
}

// 5) Update order status
$new_status = ($payment_method === 'cod') ? 'cod' : 'paid';
$upd = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
$upd->execute([$new_status, $order_id]);

// 6) Redirect to thank you page
header("Location: order_success.php?order_id={$order_id}");
exit;
