<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$id_usuario = $_SESSION['id_usuario'];

$stmt = $conn->prepare("SELECT * FROM historial_busqueda WHERE id_usuario = ? ORDER BY fecha DESC");
$stmt->execute([$id_usuario]);
$historial = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Función auxiliar para convertir los filtros en parámetros GET
function generarURLdesdeFiltro($filtroTexto) {
    // Extrae título, autor, tipo, formato
    $parametros = [];
    if (preg_match('/Título: (.*?),/', $filtroTexto, $m)) {
        $parametros['titulo'] = trim($m[1]);
    }
    if (preg_match('/Autor: (.*?),/', $filtroTexto, $m)) {
        $parametros['autor'] = trim($m[1]);
    }
    if (preg_match('/Tipo: (.*?),/', $filtroTexto, $m)) {
        $parametros['tipo'] = trim($m[1]);
    }
    if (preg_match('/Formato: (.*)/', $filtroTexto, $m)) {
        $parametros['formato'] = trim($m[1]);
    }

    return 'buscar.php?' . http_build_query($parametros);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Búsqueda | Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        body {
            padding: 40px 120px;
        }

        h2 {
            color: #0d47a1;
        }

        .entrada {
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .entrada a {
            color: #1976d2;
            font-weight: bold;
            text-decoration: none;
        }

        .entrada a:hover {
            text-decoration: underline;
        }

        time {
            font-size: 0.9em;
            color: #777;
            display: block;
            margin-top: 5px;
        }
        
        .logout-flotante {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #d32f2f;
        color: white;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: bold;
        text-decoration: none;
        box-shadow: 0 0 10px rgba(0,0,0,0.15);
        transition: background-color 0.2s, transform 0.2s;
        z-index: 1000;
        }

        .logout-flotante:hover {
            background-color: #b71c1c;
            transform: scale(1.05);
        }

    </style>
</head>
<body>

    <!-- Logo con enlace -->
    <a href="dashboard.php">
        <img src="img/logo_escuela.png" alt="Logo de la escuela" class="logo">
    </a>

    <h2>Historial de Búsqueda</h2>

    <?php if (count($historial) > 0): ?>
        <?php foreach ($historial as $h): ?>
            <?php $url = generarURLdesdeFiltro($h['filtros_aplicados']); ?>
            <div class="entrada">
                <a href="<?= $url ?>">
                    <?= htmlspecialchars($h['filtros_aplicados']) ?>
                </a>
                <time><?= $h['fecha'] ?></time>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No tienes historial de búsqueda aún.</p>
    <?php endif; ?>
        
    <a href="logout.php" class="logout-flotante">Cerrar sesión</a>

</body>
</html>
