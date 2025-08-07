<?php
session_start();
require 'db.php';

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Obtener mensajes de soporte con nombre del usuario
$stmt = $conn->query("
    SELECT m.*, u.nombre 
    FROM mensajes_soporte m
    JOIN usuarios u ON m.id_usuario = u.id_usuario
    ORDER BY m.fecha DESC
");
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes de Soporte | Admin</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            padding: 40px 80px;
        }

        h2 {
            color: #0d47a1;
            margin-bottom: 30px;
        }

        .mensaje {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
        }

        .mensaje small {
            color: #555;
        }

        .responder-btn {
            display: inline-block;
            margin-top: 10px;
            background-color: #1976d2;
            color: white;
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
        }

        .respuestas {
            background-color: #f1f8e9;
            border-left: 4px solid #689f38;
            padding: 12px 20px;
            border-radius: 6px;
            margin-top: 15px;
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

    <a href="dashboard.php">
        <img src="img/logo_escuela.png" alt="Logo" class="logo">
    </a>

    <h2>Mensajes de Soporte Recibidos</h2>

    <?php foreach ($mensajes as $mensaje): ?>
        <div class="mensaje">
            <strong><?= htmlspecialchars($mensaje['nombre']) ?></strong><br>
            <small><?= $mensaje['fecha'] ?></small>
            <p><?= nl2br(htmlspecialchars($mensaje['mensaje'])) ?></p>

            <!-- Mostrar respuestas -->
            <?php
                $respuestas_stmt = $conn->prepare("
                    SELECT r.*, u.nombre 
                    FROM respuestas_soporte r 
                    JOIN usuarios u ON r.id_admin = u.id_usuario
                    WHERE r.id_mensaje = ?
                    ORDER BY r.fecha ASC
                ");
                $respuestas_stmt->execute([$mensaje['id_mensaje']]);
                $respuestas = $respuestas_stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php if ($respuestas): ?>
                <div class="respuestas">
                    <strong>Respuestas:</strong>
                    <?php foreach ($respuestas as $r): ?>
                        <p><em><?= nl2br(htmlspecialchars($r['respuesta'])) ?></em><br>
                        <small>Por <?= htmlspecialchars($r['nombre']) ?> - <?= $r['fecha'] ?></small></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <a class="responder-btn" href="responder_soporte.php?id=<?= $mensaje['id_mensaje'] ?>">Responder</a>
        </div>
    <?php endforeach; ?>

    <a href="logout.php" class="logout-flotante">Cerrar sesión</a>
</body>
</html>
