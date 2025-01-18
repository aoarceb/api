<?php
$host = "localhost";
$user = "dbadmin";
$password = "Strong@@password123";
$dbname = "gpsdata_database";
header("Access-Control-Allow-Origin: *");


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
