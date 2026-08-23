<?php
session_start();
require_once "dbconn.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            echo "<script>alert('Email not found.');</script>";
        } elseif (!password_verify($password, $user['password'])) {
            echo "<script>alert('Incorrect password.');</script>";
        } else {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

          
            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: index.html");
            }
            exit;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
body {
  margin: 0;
  font-family: 'Inter', sans-serif;
  background: url('https://www.tapsmart.com/wp-content/uploads/2022/01/iphone-ipad-pencil.jpg') no-repeat center center/cover;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
}

.login-box {
  background: rgba(0, 0, 0, 0.5);
  border: 2px solid #007aff;
  border-radius: 20px;
  padding: 40px;
  width: 350px;
  box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(15px);
}

.login-box h2 {
  text-align: center;
  margin-bottom: 25px;
  font-size: 28px;
}

.input-wrapper {
  position: relative;
  margin-bottom: 20px;
}

.input-wrapper input {
  width: 100%;
  padding: 12px 40px 12px 10px;
  border: none;
  border-bottom: 1px solid white;
  background: transparent;
  color: white;
  font-size: 16px;
  outline: none;
}

.input-wrapper .icon {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
}

label {
  display: block;
  margin-bottom: 5px;
  font-size: 14px;
}

.options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  margin-bottom: 20px;
}

.options a {
  color: #a0d8ff;
  text-decoration: none;
}

button {
  width: 100%;
  background-color: #007aff;
  border: none;
  padding: 12px;
  border-radius: 25px;
  color: white;
  font-size: 16px;
  cursor: pointer;
  margin-bottom: 10px;
}

button:hover {
  background-color: #005fc2;
}

.register-text {
  text-align: center;
  font-size: 14px;
}

.register-text a {
  color: #a0d8ff;
  text-decoration: none;
  font-weight: 600;
}
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <h2>Login</h2>
      <form method="POST" action="login.php">
        <label>Email</label>
        <div class="input-wrapper">
          <input type="email" name="email" placeholder="Enter your email" required />
          <span class="icon">📧</span>
        </div>

        <label>Password</label>
        <div class="input-wrapper">
          <input type="password" name="password" placeholder="Enter your password" required />
          <span class="icon">🔑</span>
        </div>

        <div class="options">
          <label><input type="checkbox" /> Remember me</label>
          <a href="#">Forgot password?</a>
        </div>

        <button type="submit">Login</button>
        <p class="register-text">Don’t have an account? <a href="register.php">Register</a></p>
      </form>
    </div>
  </div>
  
</body>
</html>
