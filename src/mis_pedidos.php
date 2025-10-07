<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener todos los pedidos del usuario
$query = "SELECT p.*, pr.nombre as producto_nombre, pr.precio as precio_unitario
          FROM pedidos p 
          JOIN productos pr ON p.producto_id = pr.id 
          WHERE p.usuario_id = :user_id 
          ORDER BY p.fecha_pedido DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - Mi Proyecto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Mis Pedidos</h1>
            <nav>
                <a href="dashboard.php">Inicio</a>
                <a href="productos.php">Productos</a>
                <a href="mis_pedidos.php">Mis Pedidos</a>
                <a href="perfil.php">Mi Perfil</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </header>
        
        <main>
            <div class="orders-container">
                <h2>Historial de Pedidos</h2>
                
                <?php if (count($pedidos) > 0): ?>
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unitario</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td><?php echo $pedido['producto_nombre']; ?></td>
                                <td>$<?php echo $pedido['precio_unitario']; ?></td>
                                <td><?php echo $pedido['cantidad']; ?></td>
                                <td>$<?php echo $pedido['total']; ?></td>
                                <td>
                                    <span class="status-<?php echo $pedido['estado']; ?>">
                                        <?php echo $pedido['estado']; ?>
                                    </span>
                                </td>
                                <td><?php echo $pedido['fecha_pedido']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No tienes pedidos realizados.</p>
                    <a href="productos.php" class="btn">Ir a Comprar</a>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>