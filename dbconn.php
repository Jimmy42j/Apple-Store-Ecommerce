<?php 

$host = "localhost";
$dbname = "apple_store";
$user = "root";
$password = "";
try{
    $dsn = "mysql:host=$host; dbname=$dbname";
    $conn = new PDO($dsn, $user, $password);   
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); //row['']

}catch(PDOException $e)
{
    echo $e->getMessage();
}


?>