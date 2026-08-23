<?php
// categoryforaccessories.php
require_once "dbconn.php";

// 1) Fetch the Accessories category_id
$catStmt = $conn->prepare("SELECT category_id FROM category WHERE cname = ?");
$catStmt->execute(['Accessories']);
$catId = $catStmt->fetchColumn();
if (!$catId) {
    die("Accessories category not found.");
}

// 2) Load all accessories in that category
$stmt = $conn->prepare("
  SELECT * 
    FROM accessories 
   WHERE category_id = ?
   ORDER BY item_name
");
$stmt->execute([$catId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Accessories | mDrive</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { margin:40px; font-family:'Inter',sans-serif; background:#fff; }
    h1 { font-size:28px; margin-bottom:20px; }
    .grid {
      display:grid;
      grid-template-columns:repeat(auto-fill,minmax(200px,1fr));
      gap:30px;
    }
    .card { text-align:center; }
    .card img {
      width:100%; max-width:200px; height:auto; border-radius:8px;
    }
    .card h3 { margin:12px 0 6px; font-size:16px; color:#111; }
    .card .price { font-weight:600; color:#111; }
    .card a { text-decoration:none; color:inherit; }

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
  align-items: center;
  flex-wrap: wrap;
}
.nav-links a,
.nav-links .dropdown-btn {
  text-decoration: none;
  color: #000;
  font-size: 15px;
  cursor: pointer;
  background: none;
  border: none;
  font-family: 'Inter', sans-serif;
}
.dropdown-menu {
  position: relative;
}
.dropdown-content {
  display: none;
  position: absolute;
  background-color: white;
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
  padding: 10px;
  top: 100%;
  left: 0;
  z-index: 1000;
}
.dropdown-content a {
  display: block;
  padding: 8px 15px;
  color: black;
  text-decoration: none;
}
.dropdown-content a:hover {
  background-color: #f2f2f2;
}
.dropdown-menu:hover .dropdown-content {
  display: block;
}
.icons {
  font-size: 20px;
}

footer {
  background: #f2f2f2;
  padding: 40px 20px;
  font-size: 14px;
  color: #333;
}
.footer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 30px;
  padding-bottom: 20px;
}
footer h4 {
  margin-bottom: 10px;
  font-size: 15px;
  color: #000;
}
footer p {
  margin: 5px 0;
}
footer strong {
  display: block;
  margin-top: 10px;
}
footer .copyright {
  text-align: center;
  font-size: 13px;
  padding-top: 10px;
  border-top: 1px solid #ddd;
  color: #888;
}

  </style>
</head>
<body>
  <header class="navbar">
  <div class="logo">SH <span class="tag">| Apple Authorized Reseller</span></div>
  <nav class="nav-links">
    <a href="index.html">Home</a>
    <div class="dropdown-menu">
      <button class="dropdown-btn"><a href="shop.html">Shop ▾</a></button>
      <div class="dropdown-content">
        <a href="category.php?category_id=1">iPhone</a>
        <a href="category.php?category_id=2">iPad</a>
        <a href="category.php?category_id=3">Mac</a>
        <a href="category.php?category_id=4">Apple Watch</a>
        <a href="category.php?category_id=5">AirPods</a>
      </div>
    </div>
    <a href="categoryforaccessories.php">Accessories</a>
    <a href="aboutus.html">About</a>
  </nav>
  <div class="icons">
    <span onclick="toggleSearchBar()"><i class="fa-solid fa-magnifying-glass"></i></span>
    <span onclick="addToCart('product-001')"><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a></span>
    <span onclick="goToLogin()"><a href="login.php"><i class="fa-solid fa-user-astronaut"></i></a></span>
    <input type="text" id="searchBar" placeholder="Search..." style="display:none; margin-left:10px; padding: 5px 10px;" />
  </div>
</header>

  <h1>Accessories</h1>
  <div class="grid">
    <?php foreach($items as $a): ?>
      <div class="card">
        <a href="productforaccessories.php?item_id=<?= $a['item_id'] ?>">
          <img src="<?= htmlspecialchars($a['image_path']) ?>" alt="">
          <h3><?= htmlspecialchars($a['item_name']) ?></h3>
          <div class="price"><?= number_format($a['price'],0) ?> $</div>
        </a>
      </div>
      
    <?php endforeach; ?>
  </div>
  <footer>
  <div class="footer-grid">
    <div>
      <h4>Shop</h4>
      <p>iPhone</p>
      <p>iPad</p>
      <p>Mac</p>
      <p>Apple Watch</p>
      <p>AirPods</p>
      <p>Accessories</p>
    </div>
    <div>
      <h4>Quick Links</h4>
      <p>About</p>
      <p>Contact Us</p>
      <p>Collection</p>
      <p>Careers</p>
      <p>Trade In</p>
    </div>
    <div>
      <h4>Legal</h4>
      <p>Privacy & Policy</p>
      <p>Delivery Policy</p>
    </div>
    <div>
      <h4>Customer Care</h4>
      <p>Phone: +959 784 344 613</p>
      <p>Ig   : kyaw_moe_htut</p>
      <p>Email: moehtut423@gmail.com</p>
      <br>
      <strong>Store Timing</strong>
      <p>Open Daily: 9:00 AM to 6:30PM</p>
    </div>
  </div>
  <p class="copyright">
    © 2025 SH Group Company Limited. All Rights Reserved.
  </p>
</footer>
<script src="https://kit.fontawesome.com/325f12f506.js" crossorigin="anonymous">
  </script>

</body>
</html>
