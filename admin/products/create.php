<?php include '../db.php';

$categories = $conn->query("SELECT * FROM category");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $default_image = $_POST['default_image'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];

    $conn->query("INSERT INTO products (name, price, image, default_image, category_id, description) 
                  VALUES ('$name', '$price', '$image', '$default_image', '$category_id', '$description')");
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Product</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      padding: 40px;
    }

    .form-container {
      background: white;
      max-width: 500px;
      margin: auto;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
      color: #555;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    select {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
    }

    button {
      width: 100%;
      padding: 10px;
      background: #28a745;
      border: none;
      color: white;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover {
      background: #218838;
    }
  </style>
</head>
<body>

<div class="form-container">
  <h2>Add Product</h2>
  <form method="POST">
    <label>Name:</label>
    <input type="text" name="name" required>

    <label>Price:</label>
    <input type="number" step="0.01" name="price" required>

    <label>Image URLs (comma separated):</label>
    <textarea name="image" rows="3"></textarea>

    <label>Default Image URL:</label>
    <input type="text" name="default_image">

    <label>Category:</label>
    <select name="category_id">
      <?php while ($cat = $categories->fetch_assoc()): ?>
        <option value="<?= $cat['category_id'] ?>"><?= $cat['cname'] ?></option>
      <?php endwhile; ?>
    </select>

    <label>Description:</label>
    <textarea name="description" rows="4"></textarea>

    <button type="submit">Add</button>
  </form>
</div>

</body>
</html>
