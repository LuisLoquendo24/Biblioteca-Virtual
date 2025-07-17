<?php
require 'db.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
    $tipo_usuario = $_POST['tipo_usuario'];

    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
    $stmt->execute([$correo]);

    if ($stmt->rowCount() > 0) {
        $mensaje = 'Este correo ya está registrado.';
    } else {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contraseña, tipo_usuario) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$nombre, $correo, $contraseña, $tipo_usuario])) {
            $mensaje = 'Registro exitoso. Ahora puedes iniciar sesión.';
        } else {
            $mensaje = 'Error al registrar. Intenta de nuevo.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro | Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <!-- Logo -->
    <a href="dashboard.php">
        <img src="img/logo_escuela.png" alt="Logo de la escuela" class="logo">
    </a>
    <div class="container">
        <h2>Crear cuenta</h2>

        <?php if ($mensaje): ?>
            <p><?= $mensaje ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label for="nombre">Nombre completo:</label>
            <input type="text" name="nombre" required>

            <label for="correo">Correo electrónico:</label>
            <input type="email" name="correo" required>

            <label for="contraseña">Contraseña:</label>
            <input type="password" name="contraseña" required>

            <label for="tipo_usuario">Tipo de usuario:</label>
            <select name="tipo_usuario">
                <option value="estudiante">Estudiante</option>
                <option value="docente">Docente</option>
            </select>

            <input type="submit" value="Registrarse">
        </form>

        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
</body>
</html>
