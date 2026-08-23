<?php include '../db.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $conn->query("INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$pass', '$role')");
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add User</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f6fa; padding: 40px;">

  <div style="max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    <h2 style="text-align:center; color: #333;">Add User</h2>
    <form method="POST">
      <label style="font-weight: bold;">Name:</label><br>
      <input type="text" name="name" required
             style="width: 100%; padding: 10px; margin: 8px 0 16px 0; border: 1px solid #ccc; border-radius: 4px;"><br>

      <label style="font-weight: bold;">Email:</label><br>
      <input type="email" name="email" required
             style="width: 100%; padding: 10px; margin: 8px 0 16px 0; border: 1px solid #ccc; border-radius: 4px;"><br>

      <label style="font-weight: bold;">Password:</label><br>
      <input type="password" name="password" required
             style="width: 100%; padding: 10px; margin: 8px 0 16px 0; border: 1px solid #ccc; border-radius: 4px;"><br>

      <label style="font-weight: bold;">Role:</label><br>
      <input type="text" name="role" value="user" required
             style="width: 100%; padding: 10px; margin: 8px 0 20px 0; border: 1px solid #ccc; border-radius: 4px;"><br>

      <button type="submit"
              style="width: 100%; background-color: #28a745; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer;">
        Add
      </button>
    </form>
  </div>

</body>
</html>
