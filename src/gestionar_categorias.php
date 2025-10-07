<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../public/index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener categorías
$query = "SELECT * FROM categorias ORDER BY nombre";
$stmt = $db->prepare($query);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agregar categoría
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_categoria'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    
    $query = "INSERT INTO categorias (nombre, descripcion) VALUES (:nombre, :descripcion)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    
    if ($stmt->execute()) {
        $mensaje = "Categoría agregada exitosamente";
        header("Location: gestionar_categorias.php");
        exit();
    } else {
        $error = "Error al agregar la categoría";
    }
}

// Eliminar categoría
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_categoria'])) {
    $categoria_id = $_POST['categoria_id'];
    
    $query = "DELETE FROM categorias WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $categoria_id);
    
    if ($stmt->execute()) {
        $mensaje = "Categoría eliminada exitosamente";
        header("Location: gestionar_categorias.php");
        exit();
    } else {
        $error = "Error al eliminar la categoría";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - Mi Proyecto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Gestión de Categorías</h1>
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
                <!-- Formulario para agregar categoría -->
                <div class="add-form">
                    <h2>Agregar Nueva Categoría</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="text" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Descripción:</label>
                            <textarea name="descripcion" required></textarea>
                        </div>
                        <button type="submit" name="agregar_categoria" class="btn">Agregar Categoría</button>
                    </form>
                </div>

                <!-- Lista de categorías -->
                <div class="categories-list">
                    <h2>Lista de Categorías</h2>
                    <table class="management-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td><?php echo $categoria['id']; ?></td>
                                <td><strong><?php echo $categoria['nombre']; ?></strong></td>
                                <td><?php echo $categoria['descripcion']; ?></td>
                                <td>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="categoria_id" value="<?php echo $categoria['id']; ?>">
                                        <button type="submit" name="eliminar_categoria" class="btn-delete" 
                                                onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                            Eliminar
                                        </button>
                                    </form>
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