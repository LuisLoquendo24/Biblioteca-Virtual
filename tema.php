<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: foro.php");
    exit;
}

$id_tema = $_GET['id'];

// Obtener tema
$stmt = $conn->prepare("SELECT t.*, u.nombre FROM temas t 
                        JOIN usuarios u ON t.id_usuario = u.id_usuario 
                        WHERE t.id_tema = ?");
$stmt->execute([$id_tema]);
$tema = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tema) {
    echo "<p>Tema no encontrado.</p>";
    exit;
}

// Obtener respuestas
$respuestas_stmt = $conn->prepare("SELECT r.*, u.nombre FROM respuestas r 
                                   JOIN usuarios u ON r.id_usuario = u.id_usuario 
                                   WHERE r.id_tema = ?
                                   ORDER BY r.fecha ASC");
$respuestas_stmt->execute([$id_tema]);
$respuestas = $respuestas_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tema['titulo']) ?> | Foro</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            padding: 40px 80px;
        }

        h2 {
            color: #0d47a1;
        }

        .tema {
            background-color: #e3f2fd;
            border-left: 5px solid #1976d2;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .respuestas {
            margin-bottom: 40px;
        }

        .respuesta {
            background-color: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
            margin-bottom: 15px;
        }

        .respuesta small {
            color: #555;
        }

        form {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            max-width: 700px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        button {
            margin-top: 15px;
            background-color: #1976d2;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: #1565c0;
        }

        .volver {
            margin-top: 20px;
        }

        .volver a {
            text-decoration: none;
            color: #1976d2;
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

    <h2><?= htmlspecialchars($tema['titulo']) ?></h2>

    <div class="tema">
        <p><?= nl2br(htmlspecialchars($tema['mensaje'])) ?></p>
        <small>Publicado por <strong><?= htmlspecialchars($tema['nombre']) ?></strong> el <?= $tema['fecha'] ?></small>
    </div>

    <h3>Respuestas:</h3>
    <div class="respuestas">
        <?php if (count($respuestas) > 0): ?>
            <?php foreach ($respuestas as $r): ?>
                <div class="respuesta">
                    <p><?= nl2br(htmlspecialchars($r['mensaje'])) ?></p>
                    <small>Por <strong><?= htmlspecialchars($r['nombre']) ?></strong> el <?= $r['fecha'] ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aún no hay respuestas. ¡Sé el primero en responder!</p>
        <?php endif; ?>
    </div>

    <form method="POST" action="responder.php">
        <input type="hidden" name="id_tema" value="<?= $id_tema ?>">
        <label for="mensaje">Tu respuesta:</label>
        <textarea name="mensaje" rows="5" required></textarea>
        <button type="submit">Publicar Respuesta</button>
    </form>

    <div class="volver">
        <a href="foro.php">← Volver al foro</a>
    </div>

    <a href="logout.php" class="logout-flotante">Cerrar sesión</a>

</body>
</html>