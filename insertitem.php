<?php
require_once "dbconn.php";
if (!isset($_SESSION)) {
    session_start();
}

// Fetch categories from category table
$sql = "SELECT category_id, cname FROM category";
$stmt = $conn->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll();

if (isset($_POST['insertItem'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $filename = basename($_FILES['image']['name']);
        $filepath = "Product/" . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            try {
                $sql = "INSERT INTO products (name, price, image, category_id, description)
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$name, $price, $filepath, $category_id, $description]);

                $_SESSION['insertSuccess'] = "New product successfully added!";
                header("Location: shop.html");
                exit;
            } catch (PDOException $e) {
                echo "Insert failed: " . $e->getMessage();
            }
        } else {
            echo "Image upload failed.";
        }
    } else {
        echo "No image selected or upload error.";
    }
}
?>

<style>
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
    }
    .container {
        background-color: #fff;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        max-width: 600px;
    }
    h3 {
        font-weight: bold;
        text-align: center;
    }
    .form-label {
        font-weight: 500;
    }
    .btn-primary {
        background-color: #000;
        border: none;
    }
    .btn-primary:hover {
        background-color: #333;
    }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Product - Apple Store</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4">Add New Apple Product</h3>
        <form action="insertItem.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Price ($)</label>
                <input type="number" class="form-control" name="price" step="0.01" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Product Image</label>
                <input type="file" class="form-control" name="image" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select class="form-select" name="category_id" required>
                    <option disabled selected>Select category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['cname']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100" name="insertItem">Add Product</button>
        </form>
    </div>
</body>
</html>
