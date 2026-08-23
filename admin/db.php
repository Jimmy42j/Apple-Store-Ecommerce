<?php
$conn = new mysqli("localhost", "root", "", "apple_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
