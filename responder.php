<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['id_usuario'];
    $id_tema = $_POST['id_tema'];
    $mensaje = trim($_POST['mensaje']);

    if (!empty($mensaje) && is_numeric($id_tema)) {
        $stmt = $conn->prepare("INSERT INTO respuestas (id_tema, id_usuario, mensaje) VALUES (?, ?, ?)");
        $stmt->execute([$id_tema, $id_usuario, $mensaje]);
    }
    
    // Volver a la página del tema
    header("Location: tema.php?id=" . urlencode($id_tema));
    exit;
} else {
    header("Location: foro.php");
    exit;
}
