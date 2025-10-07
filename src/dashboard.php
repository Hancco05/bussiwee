<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener estadísticas del usuario
$query = "SELECT COUNT(*) as total_pedidos FROM pedidos WHERE usuario_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener últimos pedidos
$query = "SELECT p.*, pr.nombre as producto_nombre 
          FROM pedidos p 
          JOIN productos pr ON p.producto_id = pr.id 
          WHERE p.usuario_id = :user_id 
          ORDER BY p.fecha_pedido DESC 
          LIMIT 5";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$ultimos_pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <a href="productos.php">Productos</a>
                <a href="mis_pedidos.php">Mis Pedidos</a>
                <a href="perfil.php">Mi Perfil</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </header>
        
        <main>
            <div class="user-info">
                <h2>Panel de Usuario</h2>
                
                <!-- Estadísticas rápidas -->
                <div class="stats-cards">
                    <div class="stat-card">
                        <h3>Total Pedidos</h3>
                        <p class="stat-number"><?php echo $stats['total_pedidos']; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Estado</h3>
                        <p class="stat-text">Activo</p>
                    </div>
                </div>

                <div class="features">
                    <div class="feature-card">
                        <h3>?? Comprar Productos</h3>
                        <p>Explora nuestro catálogo de productos</p>
                        <a href="productos.php" class="btn">Ir a Productos</a>
                    </div>
                    <div class="feature-card">
                        <h3>?? Mis Pedidos</h3>
                        <p>Revisa tu historial y estado de pedidos</p>
                        <a href="mis_pedidos.php" class="btn">Ver Pedidos</a>
                    </div>
                    <div class="feature-card">
                        <h3>?? Mi Perfil</h3>
                        <p>Gestiona tu información personal</p>
                        <a href="perfil.php" class="btn">Editar Perfil</a>
                    </div>
                </div>

                <!-- Últimos pedidos -->
                <div class="recent-orders">
                    <h3>Últimos Pedidos</h3>
                    <?php if (count($ultimos_pedidos) > 0): ?>
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimos_pedidos as $pedido): ?>
                                <tr>
                                    <td><?php echo $pedido['producto_nombre']; ?></td>
                                    <td><?php echo $pedido['cantidad']; ?></td>
                                    <td>$<?php echo $pedido['total']; ?></td>
                                    <td><span class="status-<?php echo $pedido['estado']; ?>"><?php echo $pedido['estado']; ?></span></td>
                                    <td><?php echo $pedido['fecha_pedido']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No tienes pedidos recientes.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>