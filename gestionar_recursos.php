<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require 'db.php';

// Eliminar recurso
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $stmt = $conn->prepare("DELETE FROM recursos WHERE id_recurso = ?");
    $stmt->execute([$id]);
    header("Location: gestionar_recursos.php");
    exit;
}

// Obtener todos los recursos
$stmt = $conn->query("SELECT * FROM recursos ORDER BY id_recurso DESC");
$recursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Recursos | Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        body {
            padding: 40px 120px;
        }

        h2 {
            color: #0d47a1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #bbdefb;
        }

        .acciones a {
            margin-right: 10px;
            text-decoration: none;
            padding: 6px 10px;
            background-color: #1976d2;
            color: white;
            border-radius: 5px;
        }

        .acciones a.eliminar {
            background-color: #d32f2f;
        }

        .acciones a:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <!-- Logo con enlace -->
    <a href="dashboard.php">
        <img src="img/logo_escuela.png" alt="Logo de la escuela" class="logo">
    </a>

    <h2>Gestionar Recursos</h2>

    <table>
        <tr>
            <th>Título</th>
            <th>Autor</th>
            <th>Tipo</th>
            <th>Formato</th>
            <th>Archivo</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($recursos as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['titulo']) ?></td>
                <td><?= htmlspecialchars($r['autor']) ?></td>
                <td><?= htmlspecialchars($r['tipo']) ?></td>
                <td><?= htmlspecialchars($r['formato']) ?></td>
                <td>
                    <?php if ($r['formato'] === 'URL'): ?>
                        <a href="<?= $r['archivo'] ?>" target="_blank">Abrir enlace</a>
                    <?php else: ?>
                        <a href="uploads/<?= $r['archivo'] ?>" target="_blank">Ver archivo</a>
                    <?php endif; ?>
                </td>
                <td class="acciones">
                    <a href="editar_recurso.php?id=<?= $r['id_recurso'] ?>">Editar</a>
                    <a href="gestionar_recursos.php?eliminar=<?= $r['id_recurso'] ?>" class="eliminar" onclick="return confirm('¿Eliminar este recurso?');">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
