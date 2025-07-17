<?php
$host = "localhost";
$db = "biblioteca_virtual";
$user = "root"; // o tu usuario de MySQL
$pass = "";     // o tu contraseña

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
