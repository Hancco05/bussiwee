<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../public/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Mi Proyecto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Panel de Administración</h1>
            <nav>
                <a href="admin_panel.php">Inicio</a>
                <a href="gestionar_usuarios.php">Gestionar Usuarios</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </header>
        
        <main>
            <div class="admin-info">
                <h2>Bienvenido, Administrador <?php echo $_SESSION['user_name']; ?></h2>
                <div class="admin-features">
                    <div class="feature-card admin">
                        <h3>Gestión de Usuarios</h3>
                        <p>Administra todos los usuarios del sistema</p>
                        <a href="gestionar_usuarios.php" class="btn">Ir a Gestión</a>
                    </div>
                    <div class="feature-card admin">
                        <h3>Estadísticas</h3>
                        <p>Ver reportes y estadísticas del sistema</p>
                    </div>
                    <div class="feature-card admin">
                        <h3>Configuración</h3>
                        <p>Configuración general del sistema</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>