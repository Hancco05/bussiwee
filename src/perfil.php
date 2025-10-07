<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener datos del usuario
$query = "SELECT * FROM usuarios WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Actualizar perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_perfil'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    
    $query = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $_SESSION['user_name'] = $nombre;
        $mensaje = "Perfil actualizado exitosamente";
        // Recargar datos
        $query = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = "Error al actualizar el perfil";
    }
}

// Cambiar contraseña
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cambiar_password'])) {
    $password_actual = $_POST['password_actual'];
    $nueva_password = $_POST['nueva_password'];
    $confirmar_password = $_POST['confirmar_password'];
    
    if (password_verify($password_actual, $usuario['password'])) {
        if ($nueva_password === $confirmar_password) {
            $nueva_password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
            $query = "UPDATE usuarios SET password = :password WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':password', $nueva_password_hash);
            $stmt->bindParam(':id', $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                $mensaje_password = "Contraseña cambiada exitosamente";
            }
        } else {
            $error_password = "Las contraseñas no coinciden";
        }
    } else {
        $error_password = "La contraseña actual es incorrecta";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Mi Proyecto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Mi Perfil</h1>
            <nav>
                <a href="dashboard.php">Inicio</a>
                <a href="productos.php">Productos</a>
                <a href="mis_pedidos.php">Mis Pedidos</a>
                <a href="perfil.php">Mi Perfil</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </header>
        
        <main>
            <div class="profile-container">
                <!-- Información del perfil -->
                <div class="profile-section">
                    <h2>Información Personal</h2>
                    <?php if (isset($mensaje)): ?>
                        <div class="success"><?php echo $mensaje; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error)): ?>
                        <div class="error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="text" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Tipo de Usuario:</label>
                            <input type="text" value="<?php echo $usuario['tipo_usuario']; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Fecha de Registro:</label>
                            <input type="text" value="<?php echo $usuario['fecha_creacion']; ?>" disabled>
                        </div>
                        <button type="submit" name="actualizar_perfil" class="btn">Actualizar Perfil</button>
                    </form>
                </div>

                <!-- Cambiar contraseña -->
                <div class="profile-section">
                    <h2>Cambiar Contraseña</h2>
                    <?php if (isset($mensaje_password)): ?>
                        <div class="success"><?php echo $mensaje_password; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error_password)): ?>
                        <div class="error"><?php echo $error_password; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label>Contraseña Actual:</label>
                            <input type="password" name="password_actual" required>
                        </div>
                        <div class="form-group">
                            <label>Nueva Contraseña:</label>
                            <input type="password" name="nueva_password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirmar Nueva Contraseña:</label>
                            <input type="password" name="confirmar_password" required>
                        </div>
                        <button type="submit" name="cambiar_password" class="btn">Cambiar Contraseña</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>