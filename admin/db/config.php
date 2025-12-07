<?php
// config.php - Database Connection using PDO

$host = "localhost";
$db   = "courier_system";
$user = "root";
$pass = ""; // XAMPP default Pass

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
