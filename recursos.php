<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$stmt = $conn->query("SELECT * FROM recursos ORDER BY id_recurso DESC");
$recursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recursos | Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            padding: 40px 120px;
        }

        h2 {
            color: #0d47a1;
        }

        .recursos {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 20px;
        }

        .recurso {
            border: 1px solid #ccc;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
        }

        .recurso h3 {
            margin: 0 0 10px;
            color: #0d47a1;
        }

        .recurso p {
            margin: 5px 0;
        }

        .acciones {
            margin-top: 10px;
        }

        .acciones a {
            margin-right: 10px;
            text-decoration: none;
            background-color: #1976d2;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
        }

        .acciones a:hover {
            background-color: #1565c0;
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

    <h2>Lista de Recursos</h2>

    <div class="recursos">
        <?php if (count($recursos) > 0): ?>
            <?php foreach ($recursos as $r): ?>
                <div class="recurso">
                    <h3><?= htmlspecialchars($r['titulo']) ?></h3>
                    <p><strong>Autor:</strong> <?= htmlspecialchars($r['autor']) ?></p>
                    <p><strong>Tipo:</strong> <?= htmlspecialchars($r['tipo']) ?> | 
                       <strong>Formato:</strong> <?= htmlspecialchars($r['formato']) ?></p>

                    <div class="acciones">
                        <?php if ($r['formato'] === 'PDF' || $r['formato'] === 'EPUB'): ?>
                            <a href="uploads/<?= $r['archivo'] ?>" target="_blank">📖 Leer</a>
                            <a href="uploads/<?= $r['archivo'] ?>" download>⬇️ Descargar</a>
                        <?php else: ?>
                            <a href="<?= $r['archivo'] ?>" target="_blank">🎥 Ver Recurso</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay recursos disponibles.</p>
        <?php endif; ?>
    </div>

    <a href="logout.php" class="logout-flotante">Cerrar sesión</a>
</body>
</html>
