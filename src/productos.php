<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener productos
$query = "SELECT * FROM productos WHERE stock > 0 ORDER BY fecha_creacion DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar compra
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comprar'])) {
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];
    
    // Obtener producto
    $query = "SELECT * FROM productos WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $producto_id);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($producto && $producto['stock'] >= $cantidad) {
        $total = $producto['precio'] * $cantidad;
        
        // Crear pedido
        $query = "INSERT INTO pedidos (usuario_id, producto_id, cantidad, total) 
                  VALUES (:usuario_id, :producto_id, :cantidad, :total)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':usuario_id', $_SESSION['user_id']);
        $stmt->bindParam(':producto_id', $producto_id);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':total', $total);
        
        if ($stmt->execute()) {
            // Actualizar stock
            $nuevo_stock = $producto['stock'] - $cantidad;
            $query = "UPDATE productos SET stock = :stock WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':stock', $nuevo_stock);
            $stmt->bindParam(':id', $producto_id);
            $stmt->execute();
            
            $mensaje = "¡Compra realizada exitosamente!";
        }
    } else {
        $error = "Stock insuficiente o producto no disponible";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Mi Proyecto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Catálogo de Productos</h1>
            <nav>
                <a href="dashboard.php">Inicio</a>
                <a href="productos.php">Productos</a>
                <a href="mis_pedidos.php">Mis Pedidos</a>
                <a href="perfil.php">Mi Perfil</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </header>
        
        <main>
            <?php if (isset($mensaje)): ?>
                <div class="success"><?php echo $mensaje; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="products-grid">
                <?php foreach ($productos as $producto): ?>
                <div class="product-card">
                    <h3><?php echo $producto['nombre']; ?></h3>
                    <p class="product-description"><?php echo $producto['descripcion']; ?></p>
                    <p class="product-price">$<?php echo $producto['precio']; ?></p>
                    <p class="product-stock">Stock: <?php echo $producto['stock']; ?></p>
                    
                    <form method="POST" class="buy-form">
                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                        <div class="form-group">
                            <label>Cantidad:</label>
                            <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>" required>
                        </div>
                        <button type="submit" name="comprar" class="btn">Comprar</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
</body>
</html>