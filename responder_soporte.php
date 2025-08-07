<?php
session_start();
require 'db.php';

// Verificación de admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

$id_mensaje = $_GET['id'] ?? null;

if (!$id_mensaje || !is_numeric($id_mensaje)) {
    header("Location: mensajes_soporte_admin.php");
    exit;
}

// Obtener el mensaje original
$stmt = $conn->prepare("
    SELECT m.*, u.nombre 
    FROM mensajes_soporte m 
    JOIN usuarios u ON m.id_usuario = u.id_usuario 
    WHERE m.id_mensaje = ?
");
$stmt->execute([$id_mensaje]);
$mensaje = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mensaje) {
    echo "Mensaje no encontrado.";
    exit;
}

// Procesar respuesta
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $respuesta = trim($_POST['respuesta']);

    if (!empty($respuesta)) {
        $stmt = $conn->prepare("INSERT INTO respuestas_soporte (id_mensaje, id_admin, respuesta) VALUES (?, ?, ?)");
        $stmt->execute([$id_mensaje, $_SESSION['id_usuario'], $respuesta]);

        header("Location: mensajes_soporte_admin.php");
        exit;
    } else {
        $error = "La respuesta no puede estar vacía.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responder Mensaje | Soporte</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            padding: 40px 80px;
        }

        h2 {
            color: #0d47a1;
        }

        .mensaje {
            background-color: #e3f2fd;
            border-left: 5px solid #1976d2;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
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

        .error {
            color: red;
            margin-top: 10px;
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

    <h2>Responder a: <?= htmlspecialchars($mensaje['nombre']) ?></h2>

    <div class="mensaje">
        <p><strong>Mensaje:</strong> <?= nl2br(htmlspecialchars($mensaje['mensaje'])) ?></p>
        <small>Enviado el: <?= $mensaje['fecha'] ?></small>
    </div>

    <form method="POST">
        <label for="respuesta">Tu respuesta:</label>
        <textarea name="respuesta" rows="5" required></textarea>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <button type="submit">Enviar respuesta</button>
    </form>

    <div class="volver">
        <a href="mensajes_soporte_admin.php">← Volver a mensajes</a>
    </div>

    <a href="logout.php" class="logout-flotante">Cerrar sesión</a>

</body>
</html>
