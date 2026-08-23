<?php
session_start();
require_once "dbconn.php";
if (empty($_SESSION['user_id'])) exit;

$id = intval($_GET['id']);
$stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_item_id = ?");
$stmt->execute([$id]);

header("Location: cart.php");
exit;
