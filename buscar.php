<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

$titulo = $_GET['titulo'] ?? '';
$autor = $_GET['autor'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$formato = $_GET['formato'] ?? '';

// Consulta dinámica
$sql = "SELECT * FROM recursos WHERE 1=1";
$params = [];

if ($titulo) {
    $sql .= " AND titulo LIKE ?";
    $params[] = "%$titulo%";
}
if ($autor) {
    $sql .= " AND autor LIKE ?";
    $params[] = "%$autor%";
}
if ($tipo) {
    $sql .= " AND tipo = ?";
    $params[] = $tipo;
}
if ($formato) {
    $sql .= " AND formato = ?";
    $params[] = $formato;
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Guardar historial
if ($titulo || $autor || $tipo || $formato) {
    $descripcion = "Título: $titulo, Autor: $autor, Tipo: $tipo, Formato: $formato";
    $id_usuario = $_SESSION['id_usuario'];

    $stmtHist = $conn->prepare("INSERT INTO historial_busqueda (id_usuario, termino_busqueda, filtros_aplicados) VALUES (?, ?, ?)");
    $stmtHist->execute([$id_usuario, $titulo . " " . $autor, $descripcion]);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Búsqueda Avanzada | Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        body {
            padding: 40px 120px;
        }

        h2 {
            color: #0d47a1;
        }

        .filtros input, .filtros select {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .recursos {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 20px;
        }

        .recurso {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .acciones a {
            margin-right: 10px;
            background-color: #1976d2;
            padding: 6px 10px;
            color: white;
            border-radius: 5px;
            text-decoration: none;
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

    <h2>Búsqueda Avanzada</h2>

    <form method="GET" class="filtros">
        <label for="titulo">Título:</label>
        <input type="text" name="titulo" value="<?= htmlspecialchars($titulo) ?>">

        <label for="autor">Autor:</label>
        <input type="text" name="autor" value="<?= htmlspecialchars($autor) ?>">

        <label for="tipo">Tipo:</label>
        <select name="tipo">
            <option value="">-- Todos --</option>
            <option value="Libro" <?= $tipo == 'Libro' ? 'selected' : '' ?>>Libro</option>
            <option value="Artículo" <?= $tipo == 'Artículo' ? 'selected' : '' ?>>Artículo</option>
            <option value="Video" <?= $tipo == 'Video' ? 'selected' : '' ?>>Video</option>
            <option value="Podcast" <?= $tipo == 'Podcast' ? 'selected' : '' ?>>Podcast</option>
        </select>

        <label for="formato">Formato:</label>
        <select name="formato">
            <option value="">-- Todos --</option>
            <option value="PDF" <?= $formato == 'PDF' ? 'selected' : '' ?>>PDF</option>
            <option value="EPUB" <?= $formato == 'EPUB' ? 'selected' : '' ?>>EPUB</option>
            <option value="MP4" <?= $formato == 'MP4' ? 'selected' : '' ?>>MP4</option>
            <option value="MP3" <?= $formato == 'MP3' ? 'selected' : '' ?>>MP3</option>
            <option value="URL" <?= $formato == 'URL' ? 'selected' : '' ?>>URL</option>
        </select>

        <input type="submit" value="Buscar">
    </form>

    <div class="recursos">
        <?php if (count($resultados) > 0): ?>
            <?php foreach ($resultados as $r): ?>
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
            <p>No se encontraron resultados.</p>
        <?php endif; ?>
    </div>

    <a href="logout.php" class="logout-flotante">Cerrar sesión</a>
</body>
</html>
