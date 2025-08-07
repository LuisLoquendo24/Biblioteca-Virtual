<?php
require 'db.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $token = bin2hex(random_bytes(16));

        $stmt = $conn->prepare("UPDATE usuarios SET token_recuperacion = ? WHERE correo = ?");
        $stmt->execute([$token, $correo]);

        $mensaje = "Enlace para restablecer tu contraseña: 
        <a href='restablecer.php?token=$token'>Haz clic aquí</a>";
    } else {
        $mensaje = "Correo no registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
    <img src="img/logo_escuela.png" alt="Logo de la escuela" class="logo">

    <div class="container">
        <h2>Recuperar contraseña</h2>

        <?php if ($mensaje): ?>
            <p><?= $mensaje ?></p>
        <?php endif; ?>

        <form action="recuperar.php" method="POST">
            <label for="correo">Correo electrónico:</label>
            <input type="email" name="correo" required>

            <input type="submit" value="Enviar enlace de recuperación">
        </form>

        <p><a href="login.php">Volver al login</a></p>
    </div>
</body>
</html>
