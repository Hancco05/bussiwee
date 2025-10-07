<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../public/index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener todos los usuarios
$query = "SELECT * FROM usuarios ORDER BY fecha_creacion DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Mi Proyecto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Gestión de Usuarios</h1>
            <nav>
                <a href="admin_panel.php">Inicio</a>
                <a href="gestionar_usuarios.php">Gestionar Usuarios</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </header>
        
        <main>
            <div class="users-management">
                <h2>Lista de Usuarios</h2>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td><?php echo $usuario['nombre']; ?></td>
                            <td><?php echo $usuario['email']; ?></td>
                            <td><?php echo $usuario['tipo_usuario']; ?></td>
                            <td><?php echo $usuario['fecha_creacion']; ?></td>
                            <td>
                                <button class="btn-edit">Editar</button>
                                <button class="btn-delete">Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>