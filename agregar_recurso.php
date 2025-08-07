<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require 'db.php';
$mensaje = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $tipo = $_POST['tipo'];
    $formato = $_POST['formato'];
    $archivoFinal = '';

    if ($formato === 'URL') {
        $archivoFinal = $_POST['url'];
    } elseif (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = basename($_FILES['archivo']['name']);
        $rutaDestino = 'uploads/' . $nombreArchivo;

        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaDestino)) {
            $archivoFinal = $nombreArchivo;
        } else {
            $mensaje = "Error al subir el archivo.";
        }
    } else {
        $mensaje = "Archivo no válido.";
    }

    if ($archivoFinal) {
        $stmt = $conn->prepare("INSERT INTO recursos (titulo, autor, tipo, formato, archivo) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$titulo, $autor, $tipo, $formato, $archivoFinal])) {
            $mensaje = "Recurso agregado correctamente.";
        } else {
            $mensaje = "Error al guardar en la base de datos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Recurso | Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
    <a href="dashboard.php">
        <img src="img/logo_escuela.png" alt="Logo de la escuela" class="logo">
    </a>
    <div class="container">
        <h2>Agregar nuevo recurso</h2>

        <?php if ($mensaje): ?>
            <p><?= $mensaje ?></p>
        <?php endif; ?>

        <form action="agregar_recurso.php" method="POST" enctype="multipart/form-data" id="form-recurso">
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" required>

            <label for="autor">Autor:</label>
            <input type="text" name="autor" required>

            <label for="tipo">Tipo de recurso:</label>
        <select name="tipo" required>
            <option value="Libro">Libro</option>
            <option value="Artículo">Artículo</option>
            <option value="Video">Video</option>
            <option value="Podcast">Podcast</option>
        </select>

        <label for="formato">Formato:</label>
        <select name="formato" id="formato" required onchange="mostrarCampoURL()">
            <option value="PDF">PDF</option>
            <option value="EPUB">EPUB</option>
            <option value="MP4">MP4</option>
            <option value="MP3">MP3</option>
            <option value="URL">URL</option>
        </select>

    <div id="campo-archivo">
        <label for="archivo">Archivo:</label>
        <input type="file" name="archivo">
    </div>

    <div id="campo-url" style="display:none;">
        <label for="url">Enlace del recurso:</label>
        <input type="text" name="url" placeholder="https://...">
    </div>

    <input type="submit" value="Agregar Recurso">
</form>

<script>
function mostrarCampoURL() {
    const formato = document.getElementById('formato').value;
    const campoArchivo = document.getElementById('campo-archivo');
    const campoURL = document.getElementById('campo-url');

    if (formato === 'URL') {
        campoArchivo.style.display = 'none';
        campoURL.style.display = 'block';
    } else {
        campoArchivo.style.display = 'block';
        campoURL.style.display = 'none';
    }
}
</script>

    </div>
</body>
</html>
