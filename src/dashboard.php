<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="dashboard-container">
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre'], ENT_QUOTES, 'ISO-8859-1'); ?> ??</h1>

    <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
        <p>?? Estás en modo administrador.</p>
        <a href="../src/admin_panel.php">Ir al Panel de Administración</a>
    <?php else: ?>
        <p>?? Has ingresado como usuario normal.</p>
    <?php endif; ?>

    <a href="../src/logout.php">Cerrar sesión</a>
</div>
</body>
</html>
