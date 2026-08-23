<?php include '../db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>User List</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 30px;">

  <h2 style="color: #333;">Users</h2>
  <a href="create.php" style="display: inline-block; margin-bottom: 15px; background-color: #28a745; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;">+ Add User</a>

  <table cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; background-color: white; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <tr style="background-color: #007bff; color: white;">
      <th style="border: 1px solid #ddd;">ID</th>
      <th style="border: 1px solid #ddd;">Name</th>
      <th style="border: 1px solid #ddd;">Email</th>
      <th style="border: 1px solid #ddd;">Role</th>
      <th style="border: 1px solid #ddd;">Actions</th>
    </tr>
    <?php
    $result = $conn->query("SELECT * FROM users");
    while ($row = $result->fetch_assoc()):
    ?>
    <tr style="text-align: center;">
      <td style="border: 1px solid #ddd;"><?= $row['user_id'] ?></td>
      <td style="border: 1px solid #ddd;"><?= $row['name'] ?></td>
      <td style="border: 1px solid #ddd;"><?= $row['email'] ?></td>
      <td style="border: 1px solid #ddd;"><?= $row['role'] ?></td>
      <td style="border: 1px solid #ddd;">
        <a href="edit.php?id=<?= $row['user_id'] ?>" style="color: #007bff; text-decoration: none;">Edit</a> | 
        <a href="delete.php?id=<?= $row['user_id'] ?>" style="color: #dc3545; text-decoration: none;" onclick="return confirm('Are you sure?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>

</body>
</html>
