<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../public/index.php");
    exit;
}

// Verificar si el rol es admin
if ($_SESSION['usuario_rol'] !== 'admin') {
    echo "⛔ Acceso denegado. No tienes permisos para entrar aquí.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="admin-container">
    <h1>🔑 Panel de Administración</h1>
    <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre'], ENT_QUOTES, 'ISO-8859-1'); ?>.</p>
    
    <ul>
        <li><a href="gestionar_usuarios.php">👥 Gestionar Usuarios</a></li>
        <li><a href="revisar_logs.php">📜 Revisar Logs</a></li>
        <li><a href="configuracion.php">⚙️ Configuración</a></li>
    </ul>

    <a href="dashboard.php">⬅️ Volver al Dashboard</a>
</div>

</body>
</html>
