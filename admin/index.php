<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', sans-serif;
      display: flex;
      background: #f3f4f6;
      height: 100vh;
      overflow: hidden;
    }

    .sidebar {
      width: 240px;
      background: #1f2937;
      color: #fff;
      padding: 30px 20px;
    }

    .sidebar h2 {
      font-size: 22px;
      margin-bottom: 30px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .sidebar ul {
      list-style: none;
    }

    .sidebar ul li {
      margin-bottom: 20px;
    }

    .sidebar ul li a {
      color: #cbd5e1;
      text-decoration: none;
      font-size: 15px;
      transition: color 0.3s ease;
    }

    .sidebar ul li a:hover {
      color: #ffffff;
    }

    .main {
      flex: 1;
      padding: 40px;
      overflow-y: auto;
    }

    .main h1 {
      font-size: 32px;
      margin-bottom: 30px;
      font-weight: 700;
      color: #111827;
    }

    .dashboard {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
    }

    .card {
      background: #ffffff;
      padding: 30px 20px;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
      text-align: center;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    .card h3 {
      font-size: 16px;
      color: #6b7280;
      margin-bottom: 10px;
      font-weight: 500;
    }

    .card p {
      font-size: 28px;
      font-weight: 700;
      color: #10b981;
    }

    @media (max-width: 768px) {
      .sidebar {
        display: none;
      }

      body {
        flex-direction: column;
      }

      .main {
        padding: 20px;
      }

      .dashboard {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <h2>SH Mobile Shop Admin</h2>
    <ul>
      <li><a href="users/index.php">Users</a></li>
      <li><a href="products/index.php">Products</a></li>
      <li><a href="orders/index.php">Orders</a></li>
      <li><a href="/login.php">Logout</a></li>
    </ul>
  </div>

  <div class="main">
    <h1>Welcome, Admin</h1>

    <div class="dashboard">
      <?php
        $totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
        $totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
        $totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
        $totalSales = $conn->query("SELECT IFNULL(SUM(total_amount), 0) FROM orders")->fetch_row()[0];
      ?>

      <div class="card">
        <h3>Total Users</h3>
        <p><?= $totalUsers ?></p>
      </div>

      <div class="card">
        <h3>Total Products</h3>
        <p><?= $totalProducts ?></p>
      </div>

      <div class="card">
        <h3>Total Orders</h3>
        <p><?= $totalOrders ?></p>
      </div>

      <div class="card">
        <h3>Total Sales</h3>
        <p>$<?= number_format($totalSales, 2) ?></p>
      </div>
    </div>
  </div>
</body>
</html>
