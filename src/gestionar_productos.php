<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../public/index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener productos
$query = "SELECT p.*, u.nombre as creador FROM productos p LEFT JOIN usuarios u ON p.creado_por = u.id";
$stmt = $db->prepare($query);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agregar nuevo producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_producto'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    
    $query = "INSERT INTO productos (nombre, descripcion, precio, stock, creado_por) 
              VALUES (:nombre, :descripcion, :precio, :stock, :creado_por)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':creado_por', $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $mensaje = "Producto agregado exitosamente";
        header("Location: gestionar_productos.php");
        exit();
    } else {
        $error = "Error al agregar el producto";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Mi Proyecto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Gestión de Productos</h1>
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
                <!-- Formulario para agregar producto -->
                <div class="add-form">
                    <h2>Agregar Nuevo Producto</h2>
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label>Precio:</label>
                                <input type="number" name="precio" step="0.01" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descripción:</label>
                            <textarea name="descripcion" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Stock:</label>
                            <input type="number" name="stock" required>
                        </div>
                        <button type="submit" name="agregar_producto" class="btn">Agregar Producto</button>
                    </form>
                </div>

                <!-- Lista de productos -->
                <div class="products-list">
                    <h2>Lista de Productos</h2>
                    <table class="management-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Creado por</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?php echo $producto['id']; ?></td>
                                <td><?php echo $producto['nombre']; ?></td>
                                <td><?php echo $producto['descripcion']; ?></td>
                                <td>$<?php echo $producto['precio']; ?></td>
                                <td><?php echo $producto['stock']; ?></td>
                                <td><?php echo $producto['creador']; ?></td>
                                <td>
                                    <button class="btn-edit">Editar</button>
                                    <button class="btn-delete">Eliminar</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>