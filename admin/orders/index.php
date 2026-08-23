<?php include '../db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Orders</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 30px;">

  <h2 style="color: #333;">Orders</h2>

  <table cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; background-color: white; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <tr style="background-color: #343a40; color: white;">
      <th style="border: 1px solid #dee2e6;">Order ID</th>
      <th style="border: 1px solid #dee2e6;">User ID</th>
      <th style="border: 1px solid #dee2e6;">Total</th>
      <th style="border: 1px solid #dee2e6;">Status</th>
      <th style="border: 1px solid #dee2e6;">Created At</th>
      <th style="border: 1px solid #dee2e6;">Action</th>
    </tr>

    <?php
    $result = $conn->query("SELECT * FROM orders ORDER BY created_at ASC");
    while ($row = $result->fetch_assoc()):
      $color = 'black';
      if ($row['status'] === 'pending') $color = '#ffc107';
      elseif ($row['status'] === 'paid') $color = '#28a745';
      elseif ($row['status'] === 'shipped') $color = '#17a2b8';
      elseif ($row['status'] === 'delivered') $color = '#007bff';
      elseif ($row['status'] === 'cancelled') $color = '#dc3545';
    ?>
    <tr style="text-align: center;">
      <td style="border: 1px solid #dee2e6;"><?= $row['order_id'] ?></td>
      <td style="border: 1px solid #dee2e6;"><?= $row['user_id'] ?></td>
      <td style="border: 1px solid #dee2e6;">$<?= number_format($row['total_amount'], 2) ?></td>
      <td style="border: 1px solid #dee2e6; color: <?= $color ?>; font-weight: bold;"><?= $row['status'] ?></td>
      <td style="border: 1px solid #dee2e6;"><?= $row['created_at'] ?></td>
      <td style="border: 1px solid #dee2e6;">
        <a href="edit.php?id=<?= $row['order_id'] ?>" style="color: #007bff; text-decoration: none;">Edit</a> |
        <a href="delete.php?id=<?= $row['order_id'] ?>" style="color: #dc3545; text-decoration: none;" onclick="return confirm('Delete this order?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>

</body>
</html>
