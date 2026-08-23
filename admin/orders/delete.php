<?php include '../db.php'; ?>
<?php
$id = $_GET['id'];

// First delete related order details
$conn->query("DELETE FROM order_detail WHERE order_id = $id");

// Then delete the order
$conn->query("DELETE FROM orders WHERE order_id = $id");

header("Location: index.php");
?>
