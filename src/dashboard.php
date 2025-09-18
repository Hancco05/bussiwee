<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../public/index.php");
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
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre'], ENT_QUOTES, 'ISO-8859-1'); ?> &#128075;</h1>
    <p>Has ingresado correctamente al dashboard.</p>

    <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
        <p>?? Tienes privilegios de administrador.</p>
        <a href="admin_panel.php">Ir al Panel de Administraci&oacute;n</a>
    <?php else: ?>
        <p>?? Eres un usuario normal.</p>
    <?php endif; ?>

    <a href="../public/index.php">Cerrar sesi&oacute;n</a>
</div>

</body>
</html>

