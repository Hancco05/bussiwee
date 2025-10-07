<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../public/index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener estadísticas para reportes
$query = "SELECT 
            COUNT(*) as total_usuarios,
            (SELECT COUNT(*) FROM usuarios WHERE tipo_usuario = 'admin') as total_admins,
            (SELECT COUNT(*) FROM usuarios WHERE tipo_usuario = 'usuario') as total_usuarios_normales,
            (SELECT COUNT(*) FROM productos) as total_productos,
            (SELECT COUNT(*) FROM pedidos) as total_pedidos,
            (SELECT SUM(total) FROM pedidos WHERE estado = 'completado') as ingresos_totales,
            (SELECT AVG(total) FROM pedidos WHERE estado = 'completado') as promedio_venta,
            (SELECT COUNT(*) FROM pedidos WHERE estado = 'pendiente') as pedidos_pendientes,
            (SELECT COUNT(*) FROM pedidos WHERE estado = 'procesando') as pedidos_procesando,
            (SELECT COUNT(*) FROM pedidos WHERE estado = 'completado') as pedidos_completados,
            (SELECT COUNT(*) FROM pedidos WHERE estado = 'cancelado') as pedidos_cancelados";
$stmt = $db->prepare($query);
$stmt->execute();
$reportes = $stmt->fetch(PDO::FETCH_ASSOC);

// Productos más vendidos
$query = "SELECT pr.nombre, SUM(p.cantidad) as total_vendido, SUM(p.total) as ingresos
          FROM pedidos p
          JOIN productos pr ON p.producto_id = pr.id
          WHERE p.estado = 'completado'
          GROUP BY p.producto_id
          ORDER BY total_vendido DESC
          LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$productos_populares = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Mi Proyecto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Reportes y Estadísticas</h1>
            <nav>
                <a href="admin_panel.php">Inicio</a>
                <a href="gestionar_usuarios.php">Usuarios</a>
                <a href="gestionar_productos.php">Productos</a>
                <a href="gestionar_pedidos.php">Pedidos</a>
                <a href="gestionar_categorias.php">Categorías</a>
                <a href="reportes.php">Reportes</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </header>
        
        <main>
            <div class="reports-container">
                <h2>Reportes Generales del Sistema</h2>
                
                <!-- Estadísticas principales -->
                <div class="report-stats">
                    <div class="stat-card admin">
                        <h3>👥 Total Usuarios</h3>
                        <p class="stat-number"><?php echo $reportes['total_usuarios']; ?></p>
                        <p class="stat-detail">
                            Administradores: <?php echo $reportes['total_admins']; ?> | 
                            Usuarios: <?php echo $reportes['total_usuarios_normales']; ?>
                        </p>
                    </div>
                    <div class="stat-card admin">
                        <h3>📦 Total Productos</h3>
                        <p class="stat-number"><?php echo $reportes['total_productos']; ?></p>
                    </div>
                    <div class="stat-card admin">
                        <h3>🛒 Total Pedidos</h3>
                        <p class="stat-number"><?php echo $reportes['total_pedidos']; ?></p>
                    </div>
                    <div class="stat-card admin">
                        <h3>💰 Ingresos Totales</h3>
                        <p class="stat-number">$<?php echo $reportes['ingresos_totales'] ?: '0'; ?></p>
                        <p class="stat-detail">Promedio por venta: $<?php echo number_format($reportes['promedio_venta'] ?: 0, 2); ?></p>
                    </div>
                </div>

                <!-- Estados de pedidos -->
                <div class="pedidos-stats">
                    <h3>Estadísticas de Pedidos</h3>
                    <div class="stats-grid">
                        <div class="stat-mini pending">
                            <h4>Pendientes</h4>
                            <p><?php echo $reportes['pedidos_pendientes']; ?></p>
                        </div>
                        <div class="stat-mini processing">
                            <h4>Procesando</h4>
                            <p><?php echo $reportes['pedidos_procesando']; ?></p>
                        </div>
                        <div class="stat-mini completed">
                            <h4>Completados</h4>
                            <p><?php echo $reportes['pedidos_completados']; ?></p>
                        </div>
                        <div class="stat-mini cancelled">
                            <h4>Cancelados</h4>
                            <p><?php echo $reportes['pedidos_cancelados']; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Productos más vendidos -->
                <div class="popular-products">
                    <h3>Productos Más Vendidos</h3>
                    <table class="management-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Unidades Vendidas</th>
                                <th>Ingresos Generados</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos_populares as $producto): ?>
                            <tr>
                                <td><?php echo $producto['nombre']; ?></td>
                                <td><?php echo $producto['total_vendido']; ?> unidades</td>
                                <td>$<?php echo $producto['ingresos']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($productos_populares)): ?>
                            <tr>
                                <td colspan="3">No hay datos de ventas disponibles</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>