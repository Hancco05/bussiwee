<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mi Proyecto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Bienvenido, <?php echo $_SESSION['user_name']; ?></h1>
            <nav>
                <a href="dashboard.php">Inicio</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </header>
        
        <main>
            <div class="user-info">
                <h2>Panel de Usuario</h2>
                <p>Tipo de usuario: <?php echo $_SESSION['user_type']; ?></p>
                <div class="features">
                    <div class="feature-card">
                        <h3>Mi Perfil</h3>
                        <p>Gestiona tu información personal</p>
                    </div>
                    <div class="feature-card">
                        <h3>Mis Actividades</h3>
                        <p>Revisa tu historial de actividades</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>