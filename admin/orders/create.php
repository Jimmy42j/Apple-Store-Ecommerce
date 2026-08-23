<?php include '../db.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $total_amount = $_POST['total_amount'];
    $status = $_POST['status'];

    $conn->query("INSERT INTO orders (user_id, total_amount, status) 
                  VALUES ('$user_id', '$total_amount', '$status')");
    header("Location: index.php");
}
?>
<h2>Create New Order</h2>
<form method="POST">
  <label>User ID:</label>
  <input type="number" name="user_id" required><br><br>

  <label>Total Amount ($):</label>
  <input type="number" step="0.01" name="total_amount" required><br><br>

  <label>Status:</label>
  <select name="status">
    <option value="pending">Pending</option>
    <option value="paid">Paid</option>
    <option value="shipped">Shipped</option>
    <option value="delivered">Delivered</option>
    <option value="cancelled">Cancelled</option>
  </select><br><br>

  <button type="submit">Insert Order</button>
</form>
