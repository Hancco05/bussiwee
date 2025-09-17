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
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="dashboard-container">
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?> ??</h1>
    <p>Has ingresado correctamente al dashboard.</p>
    <a href="../public/index.php">Cerrar sesión</a>
</div>

</body>
</html>
