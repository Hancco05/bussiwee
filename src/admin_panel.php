<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../public/index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener estadísticas generales
$query = "SELECT 
            (SELECT COUNT(*) FROM usuarios) as total_usuarios,
            (SELECT COUNT(*) FROM productos) as total_productos,
            (SELECT COUNT(*) FROM pedidos) as total_pedidos,
            (SELECT SUM(total) FROM pedidos WHERE estado = 'completado') as ingresos_totales";
$stmt = $db->prepare($query);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Pedidos recientes
$query = "SELECT p.*, u.nombre as usuario_nombre, pr.nombre as producto_nombre
          FROM pedidos p
          JOIN usuarios u ON p.usuario_id = u.id
          JOIN productos pr ON p.producto_id = pr.id
          ORDER BY p.fecha_pedido DESC
          LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$pedidos_recientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <a href="gestionar_usuarios.php">Usuarios</a>
                <a href="gestionar_productos.php">Productos</a>
                <a href="gestionar_pedidos.php">Pedidos</a>
                <a href="gestionar_categorias.php">Categorías</a>
                <a href="sap_configuracion.php">Configuración SAP</a>
                <a href="sincronizar_sap.php">Sincronizar SAP</a>
                <a href="reportes.php">Reportes</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </header>
        
        <main>
            <div class="admin-info">
                <h2>Bienvenido, Administrador <?php echo $_SESSION['user_name']; ?></h2>
                
                <!-- Estadísticas -->
                <div class="admin-stats">
                    <div class="stat-card admin">
                        <h3>Total Usuarios</h3>
                        <p class="stat-number"><?php echo $stats['total_usuarios']; ?></p>
                    </div>
                    <div class="stat-card admin">
                        <h3>Total Productos</h3>
                        <p class="stat-number"><?php echo $stats['total_productos']; ?></p>
                    </div>
                    <div class="stat-card admin">
                        <h3>Total Pedidos</h3>
                        <p class="stat-number"><?php echo $stats['total_pedidos']; ?></p>
                    </div>
                    <div class="stat-card admin">
                        <h3>Ingresos Totales</h3>
                        <p class="stat-number">$<?php echo $stats['ingresos_totales'] ?: '0'; ?></p>
                    </div>
                </div>

                <div class="admin-features">
                    <div class="feature-card admin">
                        <h3>👥 Gestión de Usuarios</h3>
                        <p>Administra todos los usuarios del sistema</p>
                        <a href="gestionar_usuarios.php" class="btn">Ir a Gestión</a>
                    </div>
                    <div class="feature-card admin">
                        <h3>📦 Gestión de Productos</h3>
                        <p>Agrega, edita y elimina productos</p>
                        <a href="gestionar_productos.php" class="btn">Gestionar Productos</a>
                    </div>
                    <div class="feature-card admin">
                        <h3>🛒 Gestión de Pedidos</h3>
                        <p>Administra y procesa pedidos</p>
                        <a href="gestionar_pedidos.php" class="btn">Ver Pedidos</a>
                    </div>
                    <div class="feature-card admin">
                        <h3>📊 Reportes y Estadísticas</h3>
                        <p>Reportes detallados del sistema</p>
                        <a href="reportes.php" class="btn">Ver Reportes</a>
                    </div>
                    <div class="feature-card admin">
                        <h3>📁 Gestión de Categorías</h3>
                        <p>Administra categorías de productos</p>
                        <a href="gestionar_categorias.php" class="btn">Gestionar Categorías</a>
                    </div>
                    <div class="feature-card admin">
                        <h3>🔗 Integración SAP</h3>
                        <p>Configuración y sincronización con SAP Business One</p>
                        <a href="sap_configuracion.php" class="btn">Configurar SAP</a>
                    </div>
                    <div class="feature-card admin">
                        <h3>⚙️ Configuración</h3>
                        <p>Configuración general del sistema</p>
                        <a href="configuracion.php" class="btn">Configurar</a>
                    </div>
                </div>

                <!-- Pedidos recientes -->
                <div class="recent-orders">
                    <h3>Pedidos Recientes</h3>
                    <?php if (count($pedidos_recientes) > 0): ?>
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos_recientes as $pedido): ?>
                                <tr>
                                    <td><?php echo $pedido['usuario_nombre']; ?></td>
                                    <td><?php echo $pedido['producto_nombre']; ?></td>
                                    <td><?php echo $pedido['cantidad']; ?></td>
                                    <td>$<?php echo $pedido['total']; ?></td>
                                    <td>
                                        <span class="status-<?php echo $pedido['estado']; ?>">
                                            <?php echo $pedido['estado']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $pedido['fecha_pedido']; ?></td>
                                    <td>
                                        <a href="editar_pedido.php?id=<?php echo $pedido['id']; ?>" class="btn-edit">Editar</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No hay pedidos recientes.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>