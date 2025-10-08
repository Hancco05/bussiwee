<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../public/index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Obtener configuración actual del sistema
$configuraciones = [];
$query = "SELECT * FROM config_sap ORDER BY id DESC LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute();
$config_sap = $stmt->fetch(PDO::FETCH_ASSOC);

// Configuración general del sistema
$config_general = [
    'nombre_sistema' => 'Bussiwee',
    'version' => '1.0.0',
    'empresa' => 'Mi Empresa S.A.',
    'moneda' => 'USD',
    'timezone' => 'America/Mexico_City',
    'items_por_pagina' => '10'
];

// Procesar actualización de configuración SAP
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_config_sap'])) {
    $sap_server = $_POST['sap_server'];
    $sap_company_db = $_POST['sap_company_db'];
    $sap_username = $_POST['sap_username'];
    $sap_password = $_POST['sap_password'];
    $sap_port = $_POST['sap_port'];
    $sap_web_url = $_POST['sap_web_url'];
    $sap_client = $_POST['sap_client'];
    $sap_system = $_POST['sap_system'];
    $sap_language = $_POST['sap_language'];
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    if ($config_sap) {
        // Actualizar configuración existente
        $query = "UPDATE config_sap SET 
                  sap_server = :sap_server,
                  sap_company_db = :sap_company_db,
                  sap_username = :sap_username,
                  sap_password = :sap_password,
                  sap_port = :sap_port,
                  sap_web_url = :sap_web_url,
                  sap_client = :sap_client,
                  sap_system = :sap_system,
                  sap_language = :sap_language,
                  activo = :activo,
                  fecha_creacion = CURRENT_TIMESTAMP
                  WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $config_sap['id']);
    } else {
        // Insertar nueva configuración
        $query = "INSERT INTO config_sap (sap_server, sap_company_db, sap_username, sap_password, sap_port, sap_web_url, sap_client, sap_system, sap_language, activo) 
                  VALUES (:sap_server, :sap_company_db, :sap_username, :sap_password, :sap_port, :sap_web_url, :sap_client, :sap_system, :sap_language, :activo)";
        $stmt = $db->prepare($query);
    }
    
    $stmt->bindParam(':sap_server', $sap_server);
    $stmt->bindParam(':sap_company_db', $sap_company_db);
    $stmt->bindParam(':sap_username', $sap_username);
    $stmt->bindParam(':sap_password', $sap_password);
    $stmt->bindParam(':sap_port', $sap_port);
    $stmt->bindParam(':sap_web_url', $sap_web_url);
    $stmt->bindParam(':sap_client', $sap_client);
    $stmt->bindParam(':sap_system', $sap_system);
    $stmt->bindParam(':sap_language', $sap_language);
    $stmt->bindParam(':activo', $activo);
    
    if ($stmt->execute()) {
        $mensaje_sap = "✅ Configuración SAP guardada exitosamente";
        // Recargar configuración
        $query = "SELECT * FROM config_sap ORDER BY id DESC LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $config_sap = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error_sap = "❌ Error al guardar la configuración SAP";
    }
}

// Probar conexión SAP
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['probar_conexion_sap'])) {
    require_once 'sap_connection.php';
    $sap_conn = new SAPConnection();
    
    if ($sap_conn->testConnection()) {
        $mensaje_prueba = "✅ Conexión SAP exitosa";
    } else {
        $error_prueba = "❌ Error en la conexión SAP: " . $sap_conn->getLastError();
    }
}

// Configuración general del sistema
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_config_general'])) {
    // En un sistema real, guardarías esto en una tabla de configuración
    $config_general['nombre_sistema'] = $_POST['nombre_sistema'];
    $config_general['empresa'] = $_POST['empresa'];
    $config_general['moneda'] = $_POST['moneda'];
    $config_general['timezone'] = $_POST['timezone'];
    $config_general['items_por_pagina'] = $_POST['items_por_pagina'];
    
    $mensaje_general = "✅ Configuración general guardada exitosamente";
}

// Obtener estadísticas del sistema
$query = "SELECT 
            (SELECT COUNT(*) FROM usuarios) as total_usuarios,
            (SELECT COUNT(*) FROM productos) as total_productos,
            (SELECT COUNT(*) FROM pedidos) as total_pedidos,
            (SELECT COUNT(*) FROM pedidos WHERE sincronizado_sap = 1) as pedidos_sincronizados";
$stmt = $db->prepare($query);
$stmt->execute();
$estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del Sistema - Bussiwee</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .config-tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 2px solid #dee2e6;
        }
        .config-tab {
            padding: 12px 24px;
            background: #f8f9fa;
            border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            font-weight: 500;
        }
        .config-tab.active {
            background: white;
            border-bottom: 2px solid #007bff;
            color: #007bff;
        }
        .config-content {
            display: none;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .config-content.active {
            display: block;
        }
        .config-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        .config-section:last-child {
            border-bottom: none;
        }
        .system-info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .config-help {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            border-left: 4px solid #007bff;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>⚙️ Configuración del Sistema</h1>
            <nav>
                <a href="admin_panel.php">Inicio</a>
                <a href="configuracion.php" style="color: #007bff;">Configuración</a>
                <a href="sap_web_access.php">SAP Web Access</a>
                <a href="sincronizar_sap.php">Sincronizar SAP</a>
                <a href="test_sap.php">Pruebas SAP</a>
                <a href="logout.php">Cerrar Sesión</a>
            </nav>
        </header>
        
        <main>
            <!-- Información del Sistema -->
            <div class="system-info-card">
                <h2>🏢 <?php echo $config_general['nombre_sistema']; ?></h2>
                <p><strong>Empresa:</strong> <?php echo $config_general['empresa']; ?> | 
                   <strong>Versión:</strong> <?php echo $config_general['version']; ?> | 
                   <strong>Moneda:</strong> <?php echo $config_general['moneda']; ?></p>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="stats-cards">
                <div class="stat-card">
                    <h3>👥 Usuarios</h3>
                    <p class="stat-number"><?php echo $estadisticas['total_usuarios']; ?></p>
                </div>
                <div class="stat-card">
                    <h3>📦 Productos</h3>
                    <p class="stat-number"><?php echo $estadisticas['total_productos']; ?></p>
                </div>
                <div class="stat-card">
                    <h3>🛒 Pedidos</h3>
                    <p class="stat-number"><?php echo $estadisticas['total_pedidos']; ?></p>
                </div>
                <div class="stat-card">
                    <h3>🔗 Sync SAP</h3>
                    <p class="stat-number"><?php echo $estadisticas['pedidos_sincronizados']; ?></p>
                </div>
            </div>

            <!-- Pestañas de Configuración -->
            <div class="config-tabs">
                <button class="config-tab active" onclick="openTab('tab-general')">General</button>
                <button class="config-tab" onclick="openTab('tab-sap')">Integración SAP</button>
                <button class="config-tab" onclick="openTab('tab-seguridad')">Seguridad</button>
                <button class="config-tab" onclick="openTab('tab-backup')">Backup</button>
            </div>

            <!-- Contenido de pestañas -->
            
            <!-- Pestaña General -->
            <div id="tab-general" class="config-content active">
                <h2>🔧 Configuración General del Sistema</h2>
                
                <?php if (isset($mensaje_general)): ?>
                    <div class="success"><?php echo $mensaje_general; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="config-section">
                        <h3>Información Básica</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nombre del Sistema:</label>
                                <input type="text" name="nombre_sistema" value="<?php echo $config_general['nombre_sistema']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Empresa:</label>
                                <input type="text" name="empresa" value="<?php echo $config_general['empresa']; ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Moneda Principal:</label>
                                <select name="moneda">
                                    <option value="USD" <?php echo $config_general['moneda'] == 'USD' ? 'selected' : ''; ?>>USD - Dólar Americano</option>
                                    <option value="EUR" <?php echo $config_general['moneda'] == 'EUR' ? 'selected' : ''; ?>>EUR - Euro</option>
                                    <option value="CLP" <?php echo $config_general['moneda'] == 'CLP' ? 'selected' : ''; ?>>CLP - Peso CHILENO</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Zona Horaria:</label>
                                <select name="timezone">
                                    <option value="America/Santiago" <?php echo $config_general['timezone'] == 'America/Santiago' ? 'selected' : ''; ?>>Santiago</option>
                                    <option value="America/New_York" <?php echo $config_general['timezone'] == 'America/New_York' ? 'selected' : ''; ?>>New York</option>
                                    <option value="Europe/Madrid" <?php echo $config_general['timezone'] == 'Europe/Madrid' ? 'selected' : ''; ?>>Madrid</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="config-section">
                        <h3>Configuración de Visualización</h3>
                        <div class="form-group">
                            <label>Items por Página:</label>
                            <select name="items_por_pagina">
                                <option value="10" <?php echo $config_general['items_por_pagina'] == '10' ? 'selected' : ''; ?>>10 items</option>
                                <option value="25" <?php echo $config_general['items_por_pagina'] == '25' ? 'selected' : ''; ?>>25 items</option>
                                <option value="50" <?php echo $config_general['items_por_pagina'] == '50' ? 'selected' : ''; ?>>50 items</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" name="guardar_config_general" class="btn">💾 Guardar Configuración General</button>
                </form>
            </div>

            <!-- Pestaña SAP -->
            <div id="tab-sap" class="config-content">
                <h2>🔗 Configuración de Integración SAP</h2>
                
                <?php if (isset($mensaje_sap)): ?>
                    <div class="success"><?php echo $mensaje_sap; ?></div>
                <?php endif; ?>
                <?php if (isset($error_sap)): ?>
                    <div class="error"><?php echo $error_sap; ?></div>
                <?php endif; ?>
                <?php if (isset($mensaje_prueba)): ?>
                    <div class="success"><?php echo $mensaje_prueba; ?></div>
                <?php endif; ?>
                <?php if (isset($error_prueba)): ?>
                    <div class="error"><?php echo $error_prueba; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="config-section">
                        <h3>Conexión Principal SAP</h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Servidor SAP:</label>
                                <input type="text" name="sap_server" value="<?php echo $config_sap['sap_server'] ?? 'sap-server.company.com'; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Puerto:</label>
                                <input type="number" name="sap_port" value="<?php echo $config_sap['sap_port'] ?? 30015; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Base de Datos Empresa:</label>
                                <input type="text" name="sap_company_db" value="<?php echo $config_sap['sap_company_db'] ?? 'COMPANY_DB'; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Cliente SAP:</label>
                                <input type="text" name="sap_client" value="<?php echo $config_sap['sap_client'] ?? '100'; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Usuario SAP:</label>
                                <input type="text" name="sap_username" value="<?php echo $config_sap['sap_username'] ?? 'demo_user'; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Contraseña SAP:</label>
                                <input type="password" name="sap_password" value="<?php echo $config_sap['sap_password'] ?? ''; ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="config-section">
                        <h3>Configuración Web SAP</h3>
                        
                        <div class="form-group">
                            <label>URL Web SAP:</label>
                            <input type="url" name="sap_web_url" value="<?php echo $config_sap['sap_web_url'] ?? 'https://sap-web.company.com'; ?>" placeholder="https://sap-server.com">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Sistema SAP:</label>
                                <select name="sap_system">
                                    <option value="S4HANA" <?php echo ($config_sap['sap_system'] ?? 'S4HANA') == 'S4HANA' ? 'selected' : ''; ?>>S/4HANA</option>
                                    <option value="ECC" <?php echo ($config_sap['sap_system'] ?? '') == 'ECC' ? 'selected' : ''; ?>>ECC 6.0</option>
                                    <option value="BW" <?php echo ($config_sap['sap_system'] ?? '') == 'BW' ? 'selected' : ''; ?>>BW/4HANA</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Idioma:</label>
                                <select name="sap_language">
                                    <option value="ES" <?php echo ($config_sap['sap_language'] ?? 'ES') == 'ES' ? 'selected' : ''; ?>>Español</option>
                                    <option value="EN" <?php echo ($config_sap['sap_language'] ?? '') == 'EN' ? 'selected' : ''; ?>>English</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="activo" <?php echo ($config_sap['activo'] ?? 0) ? 'checked' : ''; ?>>
                            Activar integración SAP
                        </label>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" name="guardar_config_sap" class="btn">💾 Guardar Configuración SAP</button>
                        <button type="submit" name="probar_conexion_sap" class="btn btn-test">🔍 Probar Conexión SAP</button>
                    </div>
                </form>

                <!-- Estado Actual SAP -->
                <div class="config-section">
                    <h3>Estado Actual de la Integración</h3>
                    <?php if ($config_sap): ?>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                            <p><strong>Servidor:</strong> <?php echo $config_sap['sap_server']; ?></p>
                            <p><strong>Base de Datos:</strong> <?php echo $config_sap['sap_company_db']; ?></p>
                            <p><strong>Estado:</strong> 
                                <span class="status-badge <?php echo $config_sap['activo'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $config_sap['activo'] ? 'ACTIVO' : 'INACTIVO'; ?>
                                </span>
                            </p>
                            <p><strong>Última Actualización:</strong> <?php echo $config_sap['fecha_creacion']; ?></p>
                        </div>
                    <?php else: ?>
                        <p>No hay configuración SAP guardada.</p>
                    <?php endif; ?>
                </div>

                <div class="config-help">
                    <h4>💡 Información de Configuración SAP</h4>
                    <p><strong>Credenciales de Prueba:</strong></p>
                    <ul>
                        <li>Usuario: demo_user | Contraseña: demo123</li>
                        <li>Usuario: admin_sap | Contraseña: admin123</li>
                    </ul>
                    <p><strong>Para producción:</strong> Usa las credenciales reales de tu sistema SAP.</p>
                </div>
            </div>

            <!-- Pestaña Seguridad -->
            <div id="tab-seguridad" class="config-content">
                <h2>🔐 Configuración de Seguridad</h2>
                
                <div class="config-section">
                    <h3>Políticas de Contraseñas</h3>
                    <div class="form-group">
                        <label>Longitud Mínima:</label>
                        <select>
                            <option>8 caracteres</option>
                            <option selected>12 caracteres</option>
                            <option>16 caracteres</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" checked>
                            Requerir mayúsculas y minúsculas
                        </label>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" checked>
                            Requerir números y caracteres especiales
                        </label>
                    </div>
                </div>

                <div class="config-section">
                    <h3>Sesión y Timeout</h3>
                    <div class="form-group">
                        <label>Tiempo de expiración de sesión:</label>
                        <select>
                            <option>15 minutos</option>
                            <option selected>30 minutos</option>
                            <option>1 hora</option>
                            <option>8 horas</option>
                        </select>
                    </div>
                </div>

                <button class="btn" style="background: #28a745;">💾 Guardar Configuración de Seguridad</button>
            </div>

            <!-- Pestaña Backup -->
            <div id="tab-backup" class="config-content">
                <h2>💾 Configuración de Backup</h2>
                
                <div class="config-section">
                    <h3>Backup Automático</h3>
                    <div class="form-group">
                        <label>Frecuencia de Backup:</label>
                        <select>
                            <option>Diario</option>
                            <option selected>Semanal</option>
                            <option>Mensual</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Hora de Backup:</label>
                        <input type="time" value="02:00">
                    </div>
                </div>

                <div class="config-section">
                    <h3>Backup Manual</h3>
                    <p>Realiza un backup manual de la base de datos:</p>
                    <button class="btn" style="background: #17a2b8;">📥 Descargar Backup</button>
                    <button class="btn" style="background: #28a745;">🔄 Generar Backup Ahora</button>
                </div>

                <div class="config-section">
                    <h3>Últimos Backups</h3>
                    <table class="management-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tamaño</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo date('Y-m-d H:i:s'); ?></td>
                                <td>2.5 MB</td>
                                <td><span class="status-active">Completado</span></td>
                                <td>
                                    <button class="btn-edit">Descargar</button>
                                    <button class="btn-delete">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
    function openTab(tabName) {
        // Ocultar todos los contenidos
        var contents = document.getElementsByClassName('config-content');
        for (var i = 0; i < contents.length; i++) {
            contents[i].classList.remove('active');
        }
        
        // Remover active de todas las pestañas
        var tabs = document.getElementsByClassName('config-tab');
        for (var i = 0; i < tabs.length; i++) {
            tabs[i].classList.remove('active');
        }
        
        // Mostrar contenido seleccionado y activar pestaña
        document.getElementById(tabName).classList.add('active');
        event.currentTarget.classList.add('active');
    }
    </script>
</body>
</html>