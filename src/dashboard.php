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
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="dashboard-container">
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre'], ENT_QUOTES, 'ISO-8859-1'); ?> ??</h1>
    <p>Has ingresado correctamente como usuario normal.</p>
    <a href="../public/index.php">Cerrar sesión</a>
</div>
</body>
</html>

