<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../public/index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

require_once 'sap_connection.php';
$sap_conn = new SAPConnection();

// Sincronizar productos desde SAP
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sincronizar_productos'])) {
    $resultado = $sap_conn->sincronizarProductos();
    
    if ($resultado !== false) {
        $mensaje_productos = "✅ Productos sincronizados: " . $resultado . " nuevos productos";
    } else {
        $error_productos = "❌ Error sincronizando productos: " . $sap_conn->getLastError();
    }
}

// Sincronizar pedidos pendientes
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sincronizar_pedidos'])) {
    // Obtener pedidos pendientes de sincronización
    $query = "SELECT id FROM pedidos WHERE sincronizado_sap = 0 AND estado = 'completado'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $pedidos_pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $sincronizados = 0;
    $errores = 0;
    
    foreach ($pedidos_pendientes as $pedido) {
        $resultado = $sap_conn->sincronizarPedido($pedido['id']);
        
        if ($resultado !== false) {
            $sincronizados++;
        } else {
            $errores++;
        }
    }
    
    $mensaje_pedidos = "✅ Pedidos sincronizados: " . $sincronizados . " exitosos, " . $errores . " errores";
}

// Obtener estadísticas de sincronización
$query = "SELECT 
            COUNT(*) as total_pedidos,
            SUM(sincronizado_sap) as pedidos_sincronizados,
            (SELECT COUNT(*) FROM productos WHERE sap_code IS NOT NULL) as productos_con_sap
          FROM pedidos";
$stmt = $db->prepare($query);
$stmt->execute();
$estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sincronización SAP - Mi Proyecto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Sincronización SAP</h1>
            <nav>
                <a href="admin_panel.php">Inicio</a>
                <a href="gestionar_usuarios.php">Usuarios</a>
                <a href="gestionar_productos.php">Productos</a>
                <a href="gestionar_pedidos.php">Pedidos</a>
                <a href="sap_configuracion.php">Configuración SAP</a>
                <a href="sincronizar_sap.php">Sincronizar SAP</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </header>
        
        <main>
            <div class="management-container">
                <h2>Panel de Sincronización SAP</h2>
                
                <!-- Estadísticas -->
                <div class="sap-stats">
                    <div class="stat-card admin">
                        <h3>📊 Pedidos Totales</h3>
                        <p class="stat-number"><?php echo $estadisticas['total_pedidos']; ?></p>
                    </div>
                    <div class="stat-card admin">
                        <h3>✅ Pedidos Sincronizados</h3>
                        <p class="stat-number"><?php echo $estadisticas['pedidos_sincronizados']; ?></p>
                    </div>
                    <div class="stat-card admin">
                        <h3>📦 Productos con SAP</h3>
                        <p class="stat-number"><?php echo $estadisticas['productos_con_sap']; ?></p>
                    </div>
                </div>

                <!-- Acciones de Sincronización -->
                <div class="sync-actions">
                    <div class="sync-card">
                        <h3>🔄 Sincronizar Productos</h3>
                        <p>Importar productos desde SAP Business One</p>
                        <form method="POST">
                            <button type="submit" name="sincronizar_productos" class="btn btn-sync">Sincronizar Productos</button>
                        </form>
                        <?php if (isset($mensaje_productos)): ?>
                            <div class="success"><?php echo $mensaje_productos; ?></div>
                        <?php endif; ?>
                        <?php if (isset($error_productos)): ?>
                            <div class="error"><?php echo $error_productos; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="sync-card">
                        <h3>🛒 Sincronizar Pedidos</h3>
                        <p>Enviar pedidos completados a SAP</p>
                        <form method="POST">
                            <button type="submit" name="sincronizar_pedidos" class="btn btn-sync">Sincronizar Pedidos</button>
                        </form>
                        <?php if (isset($mensaje_pedidos)): ?>
                            <div class="success"><?php echo $mensaje_pedidos; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Últimos pedidos sincronizados -->
                <div class="recent-sync">
                    <h3>Últimos Pedidos Sincronizados</h3>
                    <?php
                    $query = "SELECT p.*, u.nombre as cliente, pr.nombre as producto 
                              FROM pedidos p 
                              JOIN usuarios u ON p.usuario_id = u.id 
                              JOIN productos pr ON p.producto_id = pr.id 
                              WHERE p.sincronizado_sap = 1 
                              ORDER BY p.fecha_pedido DESC 
                              LIMIT 10";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $pedidos_sincronizados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <table class="management-table">
                        <thead>
                            <tr>
                                <th>Pedido ID</th>
                                <th>Cliente</th>
                                <th>Producto</th>
                                <th>Documento SAP</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos_sincronizados as $pedido): ?>
                            <tr>
                                <td><?php echo $pedido['id']; ?></td>
                                <td><?php echo $pedido['cliente']; ?></td>
                                <td><?php echo $pedido['producto']; ?></td>
                                <td><strong><?php echo $pedido['sap_doc_entry']; ?></strong></td>
                                <td><?php echo $pedido['fecha_pedido']; ?></td>
                                <td>
                                    <span class="status-<?php echo $pedido['estado']; ?>">
                                        <?php echo $pedido['estado']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($pedidos_sincronizados)): ?>
                            <tr>
                                <td colspan="6">No hay pedidos sincronizados</td>
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