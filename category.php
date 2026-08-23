<?php
require_once "dbconn.php";

if (!isset($_GET['category_id'])) {
    die("Category not specified.");
}

$category_id = $_GET['category_id'];
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : PHP_FLOAT_MAX;

// Fetch category name
$cat_stmt = $conn->prepare("SELECT cname FROM category WHERE category_id = ?");
$cat_stmt->execute([$category_id]);
$category = $cat_stmt->fetchColumn();

// Fetch products with price filtering
$stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ? AND price >= ? AND price <= ?");
$stmt->execute([$category_id, $min_price, $max_price]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?php echo htmlspecialchars($category); ?> Products</title>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      background: #fff;
      color: #000;
      padding: 20px;
    }
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 40px;
      background-color: #fff;
      border-bottom: 1px solid #ddd;
      position: sticky;
      top: 0;
      z-index: 999;
    }
    .logo {
      font-size: 28px;
      font-weight: bold;
    }
    .logo .tag {
      font-size: 14px;
      font-weight: normal;
      color: gray;
      margin-left: 10px;
    }
    .nav-links {
      display: flex;
      gap: 20px;
    }
    .nav-links a {
      text-decoration: none;
      color: #000;
    }
    .icons {
      display: flex;
      gap: 10px;
    }
    .icons span {
      font-size: 20px;
      cursor: pointer;
    }
    .layout-wrapper {
      display: flex;
      gap: 30px;
      margin-top: 40px;
    }
    .filter-box {
      min-width: 220px;
      border: 1px solid #ccc;
      padding: 20px;
      border-radius: 10px;
    }
    .filter-box input[type="number"] {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      margin-bottom: 15px;
    }
    .product-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
    }
    .product-card {
      width: 250px;
      background-color: #fff;
      border: 1px solid #eee;
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
    }
    .product-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .product-card img {
      max-width: 100%;
      height: auto;
      border-radius: 10px;
    }
    .product-card h3 {
      font-size: 18px;
      margin: 15px 0 10px;
      font-weight: bold;
      color: #222;
    }
    .product-card p {
      margin: 5px 0;
      font-size: 15px;
      color: #555;
    }
    .cart-btn {
      position: relative;
      overflow: hidden;
      padding: 10px 20px;
      font-size: 14px;
      font-weight: 600;
      color: #000;
      background-color: #fff;
      border: 2px solid #000;
      border-radius: 30px;
      cursor: pointer;
      z-index: 1;
      transition: color 0.4s ease;
    }
    .cart-btn::before {
      content: "";
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background-color: #000;
      transition: all 0.4s ease;
      z-index: -1;
    }
    .cart-btn:hover {
      color: #fff;
    }
    .cart-btn:hover::before {
      left: 0;
    }

    .main-container {
  display: flex;
  gap: 40px;
  padding: 20px 40px;
}

.filter-box {
  flex: 0 0 30%;
  max-width: 300px;
  background: #f9f9f9;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}

.filter-box h4 {
  margin-bottom: 15px;
  font-size: 18px;
}

.filter-box label {
  display: block;
  margin-top: 10px;
  font-weight: 500;
}

.filter-box input {
  width: 100%;
  padding: 8px 12px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

  </style>
  <script>
    function toggleSearchBar() {
  const navSearch = document.querySelector('.nav-search');
  navSearch.classList.toggle('active');
  const input = document.getElementById('searchBar');
  if (navSearch.classList.contains('active')) {
    input.style.width = '180px';
    input.style.opacity = '1';
    input.focus();
  } else {
    input.style.width = '0';
    input.style.opacity = '0';
    input.value = '';
  }
}

function goToLogin() {
  window.location.href = "login.php";
}

function handleSearchEnter(event) {
  if (event.key === 'Enter') {
    const keyword = event.target.value.toLowerCase().trim();
    const categoryMap = {
      "iphone": 1,
      "ipad": 2,
      "mac": 3,
      "watch": 4,
      "apple watch": 4,
      "airpods": 5,
      "accessories": "accessory"
    };

    if (categoryMap[keyword]) {
      if (categoryMap[keyword] === "accessory") {
        window.location.href = "categoryforaccessories.php";
      } else {
        window.location.href = `category.php?category_id=${categoryMap[keyword]}`;
      }
    } else {
      alert("Category not found. Try: iPhone, iPad, Mac, Watch, AirPods, Accessories.");
    }
  }
}

  </script>
</head>
<body>
<div class="navbar">
  <div class="logo">SH <span class="tag">| Apple Authorized Reseller</span></div>
  <div class="nav-links">
    <a href="index.html">Home</a>
    <a href="shop.html">Shop</a>
    <a href="categoryforaccessories.php">Accessories</a>
    <a href="aboutus.html">About</a>
  </div>
  <div class="right-section" style="display: flex; align-items: center; gap: 15px;">
  <div class="nav-search" style="position: relative; display: flex; align-items: center;">
    <input type="text" id="searchBar" class="search-input" placeholder="Search..." onkeydown="handleSearchEnter(event)" 
      style="width: 0; padding: 8px 10px; border-radius: 25px; border: 1px solid #ccc; outline: none; transition: width 0.3s ease; opacity: 0;" />
    <i class="fa-solid fa-magnifying-glass" onclick="toggleSearchBar()" style="cursor: pointer; margin-left: 10px;"></i>
  </div>
  <div class="icons">
    <span><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a></span>
    <span onclick="goToLogin()"><a href="login.php"><i class="fa-solid fa-user-astronaut"></i></a></span>
  </div>
</div>

</div>

<h2 style="margin-top: 30px; font-size: 24px;"><?php echo htmlspecialchars($category); ?> Collection</h2>

<div class="layout-wrapper">
  <!-- Filter Sidebar -->
  <form method="GET" action="category.php" class="filter-box">
    <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category_id); ?>">
    <h4>Filter by Price</h4>
    <label>Min Price ($)</label>
    <input type="number" name="min_price" value="<?php echo $_GET['min_price'] ?? '' ?>" />

    <label>Max Price ($)</label>
    <input type="number" name="max_price" value="<?php echo $_GET['max_price'] ?? '' ?>" />

    <button type="submit" class="cart-btn" style="width: 100%; margin-top: 10px;">Apply Filter</button>
  </form>

  <!-- Products Grid -->
  <div class="product-grid">
  <?php foreach ($products as $prod): ?>
    <a 
      href="product.php?product_id=<?php echo $prod['product_id']; ?>" 
      class="product-card" 
      style="text-decoration: none; color: inherit;"
    >
      <img src="<?php echo htmlspecialchars($prod['image']); ?>" 
           alt="<?php echo htmlspecialchars($prod['name']); ?>">
      <h3><?php echo htmlspecialchars($prod['name']); ?></h3>
      <p>$<?php echo number_format($prod['price'],2); ?></p>
      <!-- <p><?php echo htmlspecialchars($prod['description']); ?></p> -->
      <button class="cart-btn">View Details</button>
    </a>
  <?php endforeach; ?>
</div>
</div>
 <script src="https://kit.fontawesome.com/325f12f506.js" crossorigin="anonymous"></script>
</body>
</html>
