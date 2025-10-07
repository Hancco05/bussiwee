<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../public/index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener todos los pedidos
$query = "SELECT p.*, u.nombre as usuario_nombre, u.email as usuario_email, 
                 pr.nombre as producto_nombre, pr.precio as precio_unitario
          FROM pedidos p
          JOIN usuarios u ON p.usuario_id = u.id
          JOIN productos pr ON p.producto_id = pr.id
          ORDER BY p.fecha_pedido DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Actualizar estado del pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_estado'])) {
    $pedido_id = $_POST['pedido_id'];
    $nuevo_estado = $_POST['estado'];
    
    $query = "UPDATE pedidos SET estado = :estado WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':estado', $nuevo_estado);
    $stmt->bindParam(':id', $pedido_id);
    
    if ($stmt->execute()) {
        $mensaje = "Estado del pedido actualizado exitosamente";
        header("Location: gestionar_pedidos.php");
        exit();
    } else {
        $error = "Error al actualizar el estado del pedido";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos - Mi Proyecto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Gestión de Pedidos</h1>
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
            <div class="management-container">
                <h2>Todos los Pedidos</h2>
                
                <?php if (isset($mensaje)): ?>
                    <div class="success"><?php echo $mensaje; ?></div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>

                <table class="management-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><?php echo $pedido['id']; ?></td>
                            <td>
                                <strong><?php echo $pedido['usuario_nombre']; ?></strong><br>
                                <small><?php echo $pedido['usuario_email']; ?></small>
                            </td>
                            <td><?php echo $pedido['producto_nombre']; ?></td>
                            <td><?php echo $pedido['cantidad']; ?></td>
                            <td>$<?php echo $pedido['precio_unitario']; ?></td>
                            <td><strong>$<?php echo $pedido['total']; ?></strong></td>
                            <td>
                                <span class="status-<?php echo $pedido['estado']; ?>">
                                    <?php echo $pedido['estado']; ?>
                                </span>
                            </td>
                            <td><?php echo $pedido['fecha_pedido']; ?></td>
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                    <select name="estado" onchange="this.form.submit()">
                                        <option value="pendiente" <?php echo $pedido['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                        <option value="procesando" <?php echo $pedido['estado'] == 'procesando' ? 'selected' : ''; ?>>Procesando</option>
                                        <option value="completado" <?php echo $pedido['estado'] == 'completado' ? 'selected' : ''; ?>>Completado</option>
                                        <option value="cancelado" <?php echo $pedido['estado'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                    </select>
                                    <input type="hidden" name="actualizar_estado" value="1">
                                </form>
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