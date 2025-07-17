<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$nombre = $_SESSION['nombre'];
$tipo = $_SESSION['tipo_usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        body {
            padding: 40px 80px;
        }

        h2 {
            text-align: center;
            color: #0d47a1;
            margin-top: 80px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            max-width: 900px;
            margin: 0 auto;
        }

        .card {
            background-color: white;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            color: #0d47a1;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .card-icon {
            font-size: 36px;
            margin-bottom: 10px;
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

    <h2>Bienvenido, <?= htmlspecialchars($nombre) ?></h2>

    <div class="grid">

        <a href="recursos.php" class="card">
            <div class="card-icon">📚</div>
            <div>Ver Recursos</div>
        </a>

        <a href="buscar.php" class="card">
            <div class="card-icon">🔍</div>
            <div>Búsqueda Avanzada</div>
        </a>

        <a href="historial.php" class="card">
            <div class="card-icon">🕘</div>
            <div>Historial de Búsqueda</div>
        </a>

        <a href="foro.php" class="card">
            <div class="card-icon">💬</div>
            <div>Foro de Discusión</div>
        </a>

        <a href="soporte.php" class="card">
            <div class="card-icon">🛠️</div>
            <div>Soporte</div>
        </a>

        <?php if ($tipo === 'administrador'): ?>
            <a href="agregar_recurso.php" class="card">
                <div class="card-icon">➕</div>
                <div>Agregar Recurso</div>
            </a>

            <a href="gestionar_recursos.php" class="card">
                <div class="card-icon">⚙️</div>
                <div>Gestionar Recursos</div>
            </a>
        <?php endif; ?>
    </div>

    <a href="logout.php" class="logout-flotante">Cerrar sesión</a>

</body>
</html>
