<?php
session_start();
require_once "dbconn.php";
if (empty($_SESSION['user_id'])) exit;

$quantities = $_POST['qty'] ?? [];
foreach ($quantities as $cart_item_id => $qty) {
    $qty = max(1,(int)$qty);
    // fetch unit_price
    $p = $conn->prepare("SELECT unit_price FROM cart_items WHERE cart_item_id = ?");
    $p->execute([$cart_item_id]);
    $unit = $p->fetchColumn();
    if ($unit) {
        $t = $unit * $qty;
        $u = $conn->prepare("
          UPDATE cart_items 
             SET quantity=?, total_price=?
           WHERE cart_item_id=?
        ");
        $u->execute([$qty, $t, $cart_item_id]);
    }
}
header("Location: cart.php");
exit;
