<?php
session_start();
require 'db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$error = '';
$exito = '';

// Enviar nuevo mensaje (respuesta del usuario)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mensaje = trim($_POST['mensaje']);

    if (!empty($mensaje)) {
        $stmt = $conn->prepare("INSERT INTO mensajes_soporte (id_usuario, mensaje) VALUES (?, ?)");
        $stmt->execute([$id_usuario, $mensaje]);
        $exito = "Mensaje enviado correctamente.";
    } else {
        $error = "Escribe un mensaje.";
    }
}

// Traer todos los mensajes del usuario y respuestas relacionadas
$stmt = $conn->prepare("
    SELECT 'usuario' as tipo, m.fecha, m.mensaje, NULL as nombre 
    FROM mensajes_soporte m
    WHERE m.id_usuario = ?
    UNION
    SELECT 'admin' as tipo, r.fecha, r.respuesta as mensaje, u.nombre 
    FROM respuestas_soporte r
    JOIN mensajes_soporte m ON r.id_mensaje = m.id_mensaje
    JOIN usuarios u ON r.id_admin = u.id_usuario
    WHERE m.id_usuario = ?
    ORDER BY fecha ASC
");
$stmt->execute([$id_usuario, $id_usuario]);
$chat = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soporte - Chat</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            padding: 40px 80px;
        }

        h2 {
            color: #0d47a1;
        }

        .chatbox {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            max-width: 700px;
        }

        .mensaje-usuario, .mensaje-admin {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            position: relative;
        }

        .mensaje-usuario {
            background-color: #e3f2fd;
            border-left: 5px solid #1976d2;
        }

        .mensaje-admin {
            background-color: #f1f8e9;
            border-left: 5px solid #43a047;
        }

        .mensaje-admin small, .mensaje-usuario small {
            color: #666;
            font-size: 0.85em;
        }

        form {
            margin-top: 30px;
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

        .error { color: red; margin-top: 10px; }
        .exito { color: green; margin-top: 10px; }

        .volver {
            margin-top: 30px;
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

        .boton-admin {
        display: inline-block;
        margin-top: 20px;
        background-color: #c62828;
        color: white;
        padding: 10px 16px;
        border-radius: 8px;
        text-decoration: none;
        }
    </style>
</head>
<body>

    <a href="dashboard.php">
        <img src="img/logo_escuela.png" alt="Logo" class="logo">
    </a>

    <h2>Soporte Técnico</h2>

    <div class="chatbox">
        <?php foreach ($chat as $mensaje): ?>
            <div class="<?= $mensaje['tipo'] === 'usuario' ? 'mensaje-usuario' : 'mensaje-admin' ?>">
                <strong>
                    <?= $mensaje['tipo'] === 'usuario' ? 'Tú' : 'Soporte' ?>
                </strong>
                <p><?= nl2br(htmlspecialchars($mensaje['mensaje'])) ?></p>
                <small><?= $mensaje['fecha'] ?></small>
            </div>
        <?php endforeach; ?>

        <form method="POST">
            <label for="mensaje"><strong>Escribe un nuevo mensaje:</strong></label>
            <textarea name="mensaje" rows="4" required></textarea>
            <?php if ($error) echo "<p class='error'>$error</p>"; ?>
            <?php if ($exito) echo "<p class='exito'>$exito</p>"; ?>
            <button type="submit">Enviar mensaje</button>
        </form>
    </div>

    <div class="volver">
        <a href="dashboard.php">← Volver al menú principal</a>
    </div>

    <?php if ($_SESSION['tipo_usuario'] === 'administrador'): ?>
    <a href="mensajes_soporte_admin.php" class="boton-admin">Ver mensajes de soporte</a>
    <?php endif; ?>

    <a href="logout.php" class="logout-flotante">Cerrar sesión</a>

</body>
</html>

