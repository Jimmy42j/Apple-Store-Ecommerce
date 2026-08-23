<?php include '../db.php';
$id = $_GET['id'];
$conn->query("DELETE FROM products WHERE product_id = $id");
header("Location: index.php");
?>