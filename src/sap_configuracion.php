<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../public/index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener configuración actual
$query = "SELECT * FROM config_sap ORDER BY id DESC LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute();
$config_sap = $stmt->fetch(PDO::FETCH_ASSOC);

// Guardar configuración
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_config'])) {
    $sap_server = $_POST['sap_server'];
    $sap_company_db = $_POST['sap_company_db'];
    $sap_username = $_POST['sap_username'];
    $sap_password = $_POST['sap_password'];
    $sap_port = $_POST['sap_port'];
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    if ($config_sap) {
        // Actualizar
        $query = "UPDATE config_sap SET 
                  sap_server = :sap_server,
                  sap_company_db = :sap_company_db,
                  sap_username = :sap_username,
                  sap_password = :sap_password,
                  sap_port = :sap_port,
                  activo = :activo
                  WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $config_sap['id']);
    } else {
        // Insertar
        $query = "INSERT INTO config_sap (sap_server, sap_company_db, sap_username, sap_password, sap_port, activo) 
                  VALUES (:sap_server, :sap_company_db, :sap_username, :sap_password, :sap_port, :activo)";
        $stmt = $db->prepare($query);
    }
    
    $stmt->bindParam(':sap_server', $sap_server);
    $stmt->bindParam(':sap_company_db', $sap_company_db);
    $stmt->bindParam(':sap_username', $sap_username);
    $stmt->bindParam(':sap_password', $sap_password);
    $stmt->bindParam(':sap_port', $sap_port);
    $stmt->bindParam(':activo', $activo);
    
    if ($stmt->execute()) {
        $mensaje = "Configuración SAP guardada exitosamente";
        // Recargar configuración
        $query = "SELECT * FROM config_sap ORDER BY id DESC LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $config_sap = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = "Error al guardar la configuración SAP";
    }
}

// Probar conexión SAP
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['probar_conexion'])) {
    require_once 'sap_connection.php';
    $sap_conn = new SAPConnection();
    
    if ($sap_conn->testConnection()) {
        $mensaje_prueba = "✅ Conexión SAP exitosa";
    } else {
        $error_prueba = "❌ Error en la conexión SAP: " . $sap_conn->getLastError();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración SAP - Mi Proyecto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Configuración SAP</h1>
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
                <h2>Configuración de Conexión SAP</h2>
                
                <?php if (isset($mensaje)): ?>
                    <div class="success"><?php echo $mensaje; ?></div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="sap-config-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Servidor SAP:</label>
                            <input type="text" name="sap_server" value="<?php echo $config_sap['sap_server'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Puerto:</label>
                            <input type="number" name="sap_port" value="<?php echo $config_sap['sap_port'] ?? 30015; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Base de Datos Empresa:</label>
                            <input type="text" name="sap_company_db" value="<?php echo $config_sap['sap_company_db'] ?? ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Usuario SAP:</label>
                            <input type="text" name="sap_username" value="<?php echo $config_sap['sap_username'] ?? ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Contraseña SAP:</label>
                        <input type="password" name="sap_password" value="<?php echo $config_sap['sap_password'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="activo" <?php echo ($config_sap['activo'] ?? 0) ? 'checked' : ''; ?>>
                            Conexión SAP activa
                        </label>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" name="guardar_config" class="btn">Guardar Configuración</button>
                        <button type="submit" name="probar_conexion" class="btn btn-test">Probar Conexión</button>
                    </div>
                </form>

                <?php if (isset($mensaje_prueba)): ?>
                    <div class="success"><?php echo $mensaje_prueba; ?></div>
                <?php endif; ?>
                <?php if (isset($error_prueba)): ?>
                    <div class="error"><?php echo $error_prueba; ?></div>
                <?php endif; ?>

                <!-- Estado actual -->
                <div class="sap-status">
                    <h3>Estado Actual de la Conexión</h3>
                    <p><strong>Servidor:</strong> <?php echo $config_sap['sap_server'] ?? 'No configurado'; ?></p>
                    <p><strong>Base de Datos:</strong> <?php echo $config_sap['sap_company_db'] ?? 'No configurado'; ?></p>
                    <p><strong>Estado:</strong> 
                        <span class="<?php echo ($config_sap['activo'] ?? false) ? 'status-completado' : 'status-cancelado'; ?>">
                            <?php echo ($config_sap['activo'] ?? false) ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>