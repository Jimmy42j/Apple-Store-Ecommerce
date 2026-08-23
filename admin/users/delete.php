<?php include '../db.php'; ?>
<?php
$id = $_GET['id'];
$result = $conn->query("SELECT COUNT(*) AS count FROM orders WHERE user_id = $id");
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
  echo "<script>alert('Cannot delete user with existing orders.'); window.location='index.php';</script>";
} else {
  $conn->query("DELETE FROM users WHERE user_id=$id");
  header("Location: index.php");
}
?>