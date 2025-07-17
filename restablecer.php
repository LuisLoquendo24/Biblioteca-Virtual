<?php
require 'db.php';

$token = $_GET['token'] ?? '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $nueva = password_hash($_POST['nueva'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE usuarios SET contraseña = ?, token_recuperacion = NULL WHERE token_recuperacion = ?");
    $stmt->execute([$nueva, $token]);

    if ($stmt->rowCount() > 0) {
        $mensaje = "Contraseña restablecida correctamente. <a href='login.php'>Inicia sesión</a>";
    } else {
        $mensaje = "Token inválido o expirado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <a href="dashboard.php">
        <img src="img/logo_escuela.png" alt="Logo de la escuela" class="logo">
    </a>
    <div class="container">
        <h2>Restablecer contraseña</h2>

        <?php if ($mensaje): ?>
            <p><?= $mensaje ?></p>
        <?php endif; ?>

        <form action="restablecer.php" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <label for="nueva">Nueva contraseña:</label>
            <input type="password" name="nueva" required>

            <input type="submit" value="Actualizar contraseña">
        </form>
    </div>
</body>
</html>
