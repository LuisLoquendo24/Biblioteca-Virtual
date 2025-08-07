<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$busqueda = '';
$temas = [];

if (isset($_GET['buscar'])) {
    $busqueda = trim($_GET['buscar']);
    $stmt = $conn->prepare("
        SELECT t.*, u.nombre 
        FROM temas t 
        JOIN usuarios u ON t.id_usuario = u.id_usuario 
        WHERE t.titulo LIKE :busqueda 
           OR t.mensaje LIKE :busqueda 
           OR u.nombre LIKE :busqueda
        ORDER BY t.fecha DESC
    ");
    $stmt->execute(['busqueda' => "%$busqueda%"]);
} else {
    $stmt = $conn->query("
        SELECT t.*, u.nombre 
        FROM temas t 
        JOIN usuarios u ON t.id_usuario = u.id_usuario 
        ORDER BY t.fecha DESC
    ");
}
$temas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foro de Discusión | Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            padding: 40px 80px;
        }

        h2 {
            color: #0d47a1;
        }

        .nuevo-tema {
            display: inline-block;
            background-color: #1976d2;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 20px;
        }

        .tema {
            background: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 15px;
        }

        .tema h3 {
            margin: 0;
            color: #0d47a1;
        }

        .tema p {
            margin: 5px 0;
        }

        .ver-respuestas {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 14px;
            background-color: #64b5f6;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .ver-respuestas:hover,
        .nuevo-tema:hover {
            opacity: 0.9;
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

    <h2>Foro de Discusión</h2>
    
    <form method="GET" style="margin-bottom: 20px;">
    <input type="text" name="buscar" placeholder="Buscar por título, mensaje o autor" value="<?= htmlspecialchars($busqueda) ?>" style="padding: 10px; width: 300px; border-radius: 6px; border: 1px solid #ccc;">
    <button type="submit" style="padding: 10px 16px; border: none; background-color: #1976d2; color: white; border-radius: 6px;">Buscar</button>
    </form>

    <a href="nuevo_tema.php" class="nuevo-tema">+ Nuevo Tema</a>

    <?php if (count($temas) > 0): ?>
        <?php foreach ($temas as $tema): ?>
            <div class="tema">
                <h3><?= htmlspecialchars($tema['titulo']) ?></h3>
                <p>Publicado por <strong><?= htmlspecialchars($tema['nombre']) ?></strong> el <?= $tema['fecha'] ?></p>
                <a href="tema.php?id=<?= $tema['id_tema'] ?>" class="ver-respuestas">Ver respuestas</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay temas aún en el foro. ¡Sé el primero en publicar uno!</p>
    <?php endif; ?>

    <a href="logout.php" class="logout-flotante">Cerrar sesión</a>

</body>
</html>
