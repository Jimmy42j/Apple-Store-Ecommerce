<?php
// Show errors (for development)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DB connection
require_once "dbconn.php";

// Form handling
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.');</script>";
    } elseif ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->rowCount() > 0) {
            echo "<script>alert('Email already exists.');</script>";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed])) {
                echo "<script>alert('Registration successful!');</script>";
            } else {
                echo "<script>alert('Registration failed.');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: url('image_design/BackgroundLogin.jpg') no-repeat center center/cover;
      background-size: cover;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .register-container {
      width: 360px;
      padding: 40px;
      background: rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(10px);
      border: 1px solid #0f8cff;
      border-radius: 20px;
      color: #fff;
    }
    .register-container h2 {
      margin: 0 0 30px;
      text-align: center;
      font-size: 32px;
      font-weight: 600;
    }
    .input-wrapper {
      position: relative;
      margin-bottom: 25px;
    }
    .input-wrapper label {
      display: block;
      margin-bottom: 6px;
      font-size: 14px;
      color: #eee;
    }
    .input-wrapper input {
      width: 100%;
      padding: 10px 40px 10px 10px;
      border: none;
      border-bottom: 1px solid rgba(255,255,255,0.6);
      background: transparent;
      color: #fff;
      font-size: 16px;
      outline: none;
    }
    .input-wrapper input::placeholder {
      color: rgba(255,255,255,0.7);
    }
    .input-wrapper .icon {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      pointer-events: none;
      font-size: 18px;
      color: rgba(255,255,255,0.8);
    }
    button[type="submit"] {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      background: #0f8cff;
      border: none;
      border-radius: 30px;
      font-size: 16px;
      font-weight: 600;
      color: #fff;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    button[type="submit"]:hover {
      background: #007acc;
    }
    .login-text {
      margin-top: 18px;
      text-align: center;
      font-size: 14px;
      color: #ddd;
    }
    .login-text a {
      color: #0f8cff;
      text-decoration: none;
      font-weight: 600;
    }
    .login-text a:hover {
      text-decoration: underline;
    }
    .login-btn{
      text-decoration: none;
      color: white;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h2>Register</h2>
    <form method="POST" action="register.php">
      <div class="input-wrapper">
        <label>Full Name</label>
        <input type="text" name="name" placeholder="Enter your full name" required />
        <span class="icon">👤</span>
      </div>

      <div class="input-wrapper">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email" required />
        <span class="icon">📧</span>
      </div>

      <div class="input-wrapper">
        <label>Password</label>
        <input type="password" name="password" placeholder="Create a password" required />
        <span class="icon">🔑</span>
      </div>

      <div class="input-wrapper">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Confirm your password" required />
        <span class="icon">✅</span>
      </div>

      <button type="submit">Register</button>
      <p class="login-text">
        Already have an account? <a href="login.php">Login</a>
      </p>
    </form>
  </div>
</body>
</html>
