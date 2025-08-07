<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require 'db.php';

$mensaje = '';
$id = $_GET['id'] ?? null;

// Obtener datos del recurso
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM recursos WHERE id_recurso = ?");
    $stmt->execute([$id]);
    $recurso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recurso) {
        $mensaje = "Recurso no encontrado.";
    }
} else {
    header("Location: gestionar_recursos.php");
    exit;
}

// Procesar edición
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $tipo = $_POST['tipo'];
    $formato = $_POST['formato'];
    $archivo = $_POST['archivo']; // puede ser archivo o URL

    $stmt = $conn->prepare("UPDATE recursos SET titulo = ?, autor = ?, tipo = ?, formato = ?, archivo = ? WHERE id_recurso = ?");
    if ($stmt->execute([$titulo, $autor, $tipo, $formato, $archivo, $id])) {
        $mensaje = "Recurso actualizado correctamente.";
        header("Refresh:1; URL=gestionar_recursos.php");
    } else {
        $mensaje = "Error al actualizar el recurso.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Recurso | Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
    <a href="dashboard.php">
        <img src="img/logo_escuela.png" alt="Logo de la escuela" class="logo">
    </a>
    <div class="container">
        <h2>Editar Recurso</h2>

        <?php if ($mensaje): ?>
            <p><?= $mensaje ?></p>
        <?php endif; ?>

        <?php if ($recurso): ?>
            <form action="editar_recurso.php?id=<?= $recurso['id_recurso'] ?>" method="POST">
                <label for="titulo">Título:</label>
                <input type="text" name="titulo" value="<?= htmlspecialchars($recurso['titulo']) ?>" required>

            <label for="autor">Autor:</label>
            <input type="text" name="autor" value="<?= htmlspecialchars($recurso['autor']) ?>" required>

            <label for="tipo">Tipo:</label>
            <select name="tipo" required>
                <option value="Libro" <?= $recurso['tipo'] == 'Libro' ? 'selected' : '' ?>>Libro</option>
                <option value="Artículo" <?= $recurso['tipo'] == 'Artículo' ? 'selected' : '' ?>>Artículo</option>
                <option value="Video" <?= $recurso['tipo'] == 'Video' ? 'selected' : '' ?>>Video</option>
                <option value="Podcast" <?= $recurso['tipo'] == 'Podcast' ? 'selected' : '' ?>>Podcast</option>
            </select>

            <label for="formato">Formato:</label>
            <select name="formato" id="formato" required onchange="mostrarCampoURL()">
                <option value="PDF" <?= $recurso['formato'] == 'PDF' ? 'selected' : '' ?>>PDF</option>
                <option value="EPUB" <?= $recurso['formato'] == 'EPUB' ? 'selected' : '' ?>>EPUB</option>
                <option value="MP4" <?= $recurso['formato'] == 'MP4' ? 'selected' : '' ?>>MP4</option>
                <option value="MP3" <?= $recurso['formato'] == 'MP3' ? 'selected' : '' ?>>MP3</option>
                <option value="URL" <?= $recurso['formato'] == 'URL' ? 'selected' : '' ?>>URL</option>
            </select>

                <div id="campo-url" style="display: none;">
                    <label for="archivo">Enlace del recurso:</label>
                    <input type="text" name="archivo" value="<?= htmlspecialchars($recurso['archivo']) ?>">
                </div>

            <div id="archivo-actual" style="display: none;">
                <label>Archivo actual:</label>
                <p><?= htmlspecialchars($recurso['archivo']) ?></p>
            </div>

            <input type="submit" value="Actualizar Recurso">
        </form>

        <script>
        function mostrarCampoURL() {
            const formato = document.getElementById('formato').value;
            const campoURL = document.getElementById('campo-url');
            const archivoActual = document.getElementById('archivo-actual');

            if (formato === 'URL') {
                campoURL.style.display = 'block';
                archivoActual.style.display = 'none';
            } else {
                campoURL.style.display = 'none';
                archivoActual.style.display = 'block';
            }
        }

        // Ejecutar al cargar para aplicar visibilidad según el formato actual
        window.onload = mostrarCampoURL;
        </script>

        <?php endif; ?>
    </div>
</body>
</html>
