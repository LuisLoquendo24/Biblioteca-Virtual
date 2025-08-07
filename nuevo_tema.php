<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST['titulo']);
    $mensaje = trim($_POST['mensaje']);
    $id_usuario = $_SESSION['id_usuario'];

    if (empty($titulo) || empty($mensaje)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        $stmt = $conn->prepare("INSERT INTO temas (id_usuario, titulo, mensaje) VALUES (?, ?, ?)");
        $stmt->execute([$id_usuario, $titulo, $mensaje]);
        header("Location: foro.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Tema | Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            padding: 40px 80px;
        }

        h2 {
            color: #0d47a1;
        }

        form {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            resize: vertical;
        }

        button {
            margin-top: 20px;
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

        .error {
            color: red;
            margin-top: 10px;
        }

        .volver {
            display: block;
            text-align: center;
            margin-top: 25px;
        }

        .volver a {
            color: #1976d2;
            text-decoration: none;
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

    <h2>Nuevo Tema en el Foro</h2>

    <form method="POST" action="">
        <label for="titulo">Título del tema:</label>
        <input type="text" id="titulo" name="titulo" required>

        <label for="mensaje">Mensaje inicial:</label>
        <textarea id="mensaje" name="mensaje" rows="6" required></textarea>

        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <button type="submit">Publicar Tema</button>
    </form>

    <div class="volver">
        <a href="foro.php">← Volver al foro</a>
    </div>

    <a href="logout.php" class="logout-flotante">Cerrar sesión</a>

</body>
</html>
