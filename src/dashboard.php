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
    <title>Dashboard Usuario</title>
</head>
<body>
<h1>Bienvenido <?php echo htmlspecialchars($_SESSION['usuario_nombre'], ENT_QUOTES, 'ISO-8859-1'); ?> ??</h1>
<p>Solo puedes acceder a tu panel de usuario.</p>
<a href="../public/index.php">Cerrar sesión</a>
</body>
</html>
