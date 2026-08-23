<?php include '../db.php'; ?>
<?php
$id = $_GET['id'];
$order = $conn->query("SELECT * FROM orders WHERE order_id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $total_amount = $_POST['total_amount'];
    $status = $_POST['status'];

    $conn->query("UPDATE orders SET 
        user_id = '$user_id',
        total_amount = '$total_amount',
        status = '$status'
        WHERE order_id = $id
    ");
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Order</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f0f2f5; padding: 40px;">

  <div style="max-width: 500px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; color: #333;">Edit Order #<?= $order['order_id'] ?></h2>
    <form method="POST">
      <label style="font-weight: bold;">User ID:</label><br>
      <input type="number" name="user_id" value="<?= $order['user_id'] ?>" required
             style="width: 100%; padding: 10px; margin: 8px 0 16px 0; border: 1px solid #ccc; border-radius: 4px;"><br>

      <label style="font-weight: bold;">Total Amount ($):</label><br>
      <input type="number" name="total_amount" step="0.01" value="<?= $order['total_amount'] ?>" required
             style="width: 100%; padding: 10px; margin: 8px 0 16px 0; border: 1px solid #ccc; border-radius: 4px;"><br>

      <label style="font-weight: bold;">Status:</label><br>
      <select name="status"
              style="width: 100%; padding: 10px; margin: 8px 0 16px 0; border: 1px solid #ccc; border-radius: 4px;">
        <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="paid" <?= $order['status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
        <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
        <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
        <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
      </select><br>

      <button type="submit"
              style="width: 100%; background-color: #007bff; color: white; border: none; padding: 12px; border-radius: 4px; cursor: pointer;">
        Update
      </button>
    </form>
  </div>

</body>
</html>
