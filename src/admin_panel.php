<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../public/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Panel de Administraci&oacute;n</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="admin-container">
    <h1>🔑 Panel de Administraci&oacute;n</h1>
    <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre'], ENT_QUOTES, 'ISO-8859-1'); ?>.</p>
    
    <ul>
        <li><a href="gestionar_usuarios.php">👥 Gestionar Usuarios</a></li>
    </ul>

    <a href="dashboard.php">⬅️ Volver al Dashboard</a>
</div>

</body>
</html>
