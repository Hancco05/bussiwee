<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'usuario') {
    header("Location: ../public/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Panel de Usuario</title>
</head>
<body>
    <h1>Bienvenido Usuario, <?php echo htmlspecialchars($_SESSION['usuario_nombre'], ENT_QUOTES, 'ISO-8859-1'); ?></h1>
    <p>?? Has ingresado correctamente a tu panel personal.</p>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
