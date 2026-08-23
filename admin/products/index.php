<?php include '../db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Product List</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f9f9f9;
      padding: 40px;
      color: #333;
    }

    h2 {
      margin-bottom: 20px;
      font-weight: 600;
    }

    a.button {
      background-color: #28a745;
      color: white;
      padding: 10px 18px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: 600;
      display: inline-block;
      margin-bottom: 20px;
    }

    a.button:hover {
      background-color: #218838;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    th, td {
      padding: 12px 15px;
      border: 1px solid #ddd;
      text-align: left;
      font-size: 15px;
    }

    th {
      background-color: #f1f1f1;
      font-weight: 600;
    }

    td img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 6px;
    }

    td a {
      text-decoration: none;
      color: #007bff;
      font-weight: 500;
    }

    td a:hover {
      text-decoration: underline;
    }

    td a.delete {
      color: #dc3545;
    }

    tr:hover {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>

  <h2>Product List</h2>
  <a href="create.php" class="button">+ Add Product</a>

  <table>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Price</th>
      <th>Category</th>
      <th>Default Image</th>
      <th>Actions</th>
    </tr>

    <?php
    $result = $conn->query("SELECT p.*, c.cname FROM products p 
                            LEFT JOIN category c ON p.category_id = c.category_id 
                            ORDER BY p.created_at ASC");

    while ($row = $result->fetch_assoc()):
      $images = explode(',', $row['default_image']);
      $firstImage = trim($images[0]); // Get first image from list
    ?>
    <tr>
      <td><?= htmlspecialchars($row['product_id']) ?></td>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td>$<?= number_format($row['price'], 2) ?></td>
      <td><?= htmlspecialchars($row['cname']) ?></td>
      <td>
        <img src="<?= htmlspecialchars('/Product/' . basename($firstImage)) ?>"
     onerror="this.onerror=null; this.src='https://via.placeholder.com/50'"
     alt="Product Image">

      </td>
      <td>
        <a href="edit.php?id=<?= $row['product_id'] ?>">Edit</a> |
        <a href="delete.php?id=<?= $row['product_id'] ?>" class="delete" onclick="return confirm('Delete this product?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>

</body>
</html>
