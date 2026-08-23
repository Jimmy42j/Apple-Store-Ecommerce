<?php include '../db.php';

$id = $_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE product_id=$id")->fetch_assoc();
$categories = $conn->query("SELECT * FROM category");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $default_image = $_POST['default_image'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];

    $conn->query("UPDATE products SET 
        name='$name', price='$price', image='$image', default_image='$default_image', 
        category_id='$category_id', description='$description' 
        WHERE product_id=$id");
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Product</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f2f5;
      padding: 30px;
    }

    .form-container {
      background: #fff;
      max-width: 600px;
      margin: auto;
      padding: 25px 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 25px;
    }

    label {
      font-weight: bold;
      margin-bottom: 6px;
      display: block;
      color: #555;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    select {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    textarea {
      resize: vertical;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #007bff;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

<div class="form-container">
  <h2>Edit Product</h2>
  <form method="POST">
    <label>Name:</label>
    <input type="text" name="name" value="<?= $product['name'] ?>" required>

    <label>Price:</label>
    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>

    <label>Image URLs:</label>
    <textarea name="image" rows="3"><?= $product['image'] ?></textarea>

    <label>Default Image URL:</label>
    <input type="text" name="default_image" value="<?= $product['default_image'] ?>">

    <label>Category:</label>
    <select name="category_id">
      <?php while ($cat = $categories->fetch_assoc()): ?>
        <option value="<?= $cat['category_id'] ?>" <?= $product['category_id'] == $cat['category_id'] ? 'selected' : '' ?>>
          <?= $cat['cname'] ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label>Description:</label>
    <textarea name="description" rows="4"><?= $product['description'] ?></textarea>

    <button type="submit">Update</button>
  </form>
</div>

</body>
</html>
