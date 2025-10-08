<?php
session_start();
require_once '../config/db.php';
require_once 'sap_web_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../public/index.php");
    exit();
}

$sap_web = new SAPWebConnection();
$sap_session = null;
$user_info = null;

// Login a SAP
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sap_login'])) {
    $username = $_POST['sap_username'];
    $password = $_POST['sap_password'];
    
    $session_id = $sap_web->loginToSAP($username, $password);
    
    if ($session_id) {
        $_SESSION['sap_session_id'] = $session_id;
        $sap_session = $sap_web->getSessionData();
        $user_info = $sap_web->getUserInfo();
        $success_message = "✅ Login SAP exitoso";
    } else {
        $error_message = "❌ " . $sap_web->getLastError();
    }
}

// Ejecutar transacción
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['execute_transaction'])) {
    if (!isset($_SESSION['sap_session_id'])) {
        $error_message = "Primero debes iniciar sesión en SAP";
    } else {
        $transaction = $_POST['transaction_code'];
        $result = $sap_web->executeTransaction($transaction);
        
        if ($result) {
            $transaction_result = $result;
        } else {
            $error_message = $sap_web->getLastError();
        }
    }
}

// Obtener datos OData
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['get_odata'])) {
    if (!isset($_SESSION['sap_session_id'])) {
        $error_message = "Primero debes iniciar sesión en SAP";
    } else {
        $endpoint = $_POST['odata_endpoint'];
        $odata_result = $sap_web->getOData($endpoint);
    }
}

// Cargar sesión existente
if (isset($_SESSION['sap_session_id'])) {
    $sap_session = $sap_web->getSessionData();
    $user_info = $sap_web->getUserInfo();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAP Web Access - Mi Proyecto</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .sap-web-container {
            background: linear-gradient(135deg, #0f4c75 0%, #3282b8 100%);
            color: white;
            min-height: 100vh;
        }
        .sap-header {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            backdrop-filter: blur(10px);
        }
        .sap-login-form {
            background: white;
            color: #333;
            padding: 30px;
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .sap-dashboard {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        .sap-sidebar {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        .sap-main {
            background: white;
            color: #333;
            padding: 20px;
            border-radius: 10px;
            min-height: 500px;
        }
        .sap-module-card {
            background: rgba(255,255,255,0.2);
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .sap-module-card:hover {
            background: rgba(255,255,255,0.3);
            transform: translateX(5px);
        }
        .sap-iframe-container {
            width: 100%;
            height: 600px;
            border: none;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .transaction-panel {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .sap-badge {
            background: #ff6b6b;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            margin-left: 10px;
        }
    </style>
</head>
<body class="sap-web-container">
    <div class="dashboard-container">
        <header class="sap-header">
            <h1>🌐 SAP Web Access</h1>
            <nav>
                <a href="admin_panel.php" style="color: white;">← Volver al Panel</a>
                <a href="sap_configuracion.php" style="color: white;">Configuración</a>
                <a href="test_sap.php" style="color: white;">Pruebas</a>
            </nav>
        </header>
        
        <main>
            <?php if (isset($success_message)): ?>
                <div class="success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Panel de Login SAP -->
            <?php if (!$sap_session): ?>
            <div class="sap-login-form">
                <h2>🔐 Login al Sistema SAP</h2>
                <p>Ingresa tus credenciales de SAP para acceder al sistema</p>
                
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Usuario SAP:</label>
                            <input type="text" name="sap_username" placeholder="demo_user" required>
                            <small>Usuarios demo: demo_user, admin_sap, consultor</small>
                        </div>
                        <div class="form-group">
                            <label>Contraseña SAP:</label>
                            <input type="password" name="sap_password" placeholder="demo123" required>
                            <small>Contraseñas demo: demo123, admin123, consultor123</small>
                        </div>
                    </div>
                    <button type="submit" name="sap_login" class="btn" style="background: #0f4c75;">
                        🚀 Conectar a SAP
                    </button>
                </form>
                
                <div style="margin-top: 20px; padding: 15px; background: #e3f2fd; border-radius: 5px;">
                    <h4>💡 Información de Prueba:</h4>
                    <p><strong>Usuario:</strong> demo_user | <strong>Password:</strong> demo123</p>
                    <p><strong>Sistema:</strong> SAP S/4HANA Demo</p>
                    <p><strong>Cliente:</strong> 100</p>
                </div>
            </div>
            <?php else: ?>
            
            <!-- Dashboard SAP una vez logeado -->
            <div class="sap-dashboard">
                <!-- Sidebar -->
                <div class="sap-sidebar">
                    <!-- Información de usuario -->
                    <div style="background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <h3>👤 <?php echo $user_info['name']; ?></h3>
                        <p><strong>Rol:</strong> <?php echo $user_info['role']; ?></p>
                        <p><strong>Departamento:</strong> <?php echo $user_info['department']; ?></p>
                        <p><strong>Sesión:</strong> <?php echo substr($sap_session['session_id'], 0, 15); ?>...</p>
                    </div>
                    
                    <!-- Módulos SAP -->
                    <h3>📂 Módulos SAP</h3>
                    <div class="sap-module-card" onclick="loadSAPModule('main')">
                        🏠 Fiori Launchpad
                    </div>
                    <div class="sap-module-card" onclick="loadSAPModule('materiales')">
                        📦 Gestión de Materiales
                    </div>
                    <div class="sap-module-card" onclick="loadSAPModule('ventas')">
                        🛒 Ventas y Distribución
                    </div>
                    <div class="sap-module-card" onclick="loadSAPModule('finanzas')">
                        💰 Finanzas y Contabilidad
                    </div>
                    
                    <!-- Transacciones Rápidas -->
                    <h3 style="margin-top: 30px;">⚡ Transacciones</h3>
                    <form method="POST" class="transaction-panel">
                        <div class="form-group">
                            <label>Transacción:</label>
                            <select name="transaction_code" class="form-control">
                                <option value="MM01">MM01 - Crear Material</option>
                                <option value="VA01">VA01 - Crear Pedido Venta</option>
                                <option value="ME21N">ME21N - Orden Compra</option>
                                <option value="F-02">F-02 - Contabilizar</option>
                            </select>
                        </div>
                        <button type="submit" name="execute_transaction" class="btn" style="background: #28a745; width: 100%;">
                            🚀 Ejecutar
                        </button>
                    </form>
                    
                    <!-- OData Services -->
                    <h3>🔗 OData Services</h3>
                    <form method="POST" class="transaction-panel">
                        <div class="form-group">
                            <label>Endpoint:</label>
                            <select name="odata_endpoint" class="form-control">
                                <option value="Materials">Materials - Materiales</option>
                                <option value="SalesOrders">SalesOrders - Pedidos</option>
                                <option value="Customers">Customers - Clientes</option>
                            </select>
                        </div>
                        <button type="submit" name="get_odata" class="btn" style="background: #17a2b8; width: 100%;">
                            📊 Obtener Datos
                        </button>
                    </form>
                </div>
                
                <!-- Área principal -->
                <div class="sap-main">
                    <!-- Resultados de transacciones -->
                    <?php if (isset($transaction_result)): ?>
                    <div class="success">
                        <h4>✅ Transacción Ejecutada</h4>
                        <p><strong>Transacción:</strong> <?php echo $transaction_result['transaction']; ?></p>
                        <p><strong>Descripción:</strong> <?php echo $transaction_result['description']; ?></p>
                        <p><strong>Número Documento:</strong> <?php echo $transaction_result['document_number']; ?></p>
                        <p><strong>Mensaje:</strong> <?php echo $transaction_result['message']; ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Resultados OData -->
                    <?php if (isset($odata_result)): ?>
                    <div class="info">
                        <h4>📊 Datos OData</h4>
                        <table class="management-table">
                            <thead>
                                <tr>
                                    <?php if (!empty($odata_result)): ?>
                                        <?php foreach (array_keys($odata_result[0]) as $header): ?>
                                            <th><?php echo $header; ?></th>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($odata_result as $row): ?>
                                    <tr>
                                        <?php foreach ($row as $value): ?>
                                            <td><?php echo $value; ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Vista Web de SAP -->
                    <h3>🌐 SAP Web Interface 
                        <span class="sap-badge">S/4HANA</span>
                        <span class="sap-badge" style="background: #28a745;">Online</span>
                    </h3>
                    
                    <div class="sap-iframe-container" id="sapFrame">
                        <div style="display: flex; justify-content: center; align-items: center; height: 100%; background: #f8f9fa; border-radius: 8px;">
                            <div style="text-align: center;">
                                <h2>🖥️ SAP Web Access</h2>
                                <p>Selecciona un módulo del menú lateral para comenzar</p>
                                <p><small>En un entorno real, aquí se cargaría SAP Fiori Launchpad</small></p>
                                <div style="margin-top: 20px;">
                                    <button onclick="loadSAPModule('main')" class="btn" style="background: #0f4c75;">
                                        🚀 Abrir SAP Fiori
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
    function loadSAPModule(module) {
        const frame = document.getElementById('sapFrame');
        
        // Simulación de carga de módulos SAP
        const moduleContents = {
            'main': `
                <div style="padding: 20px; background: #f0f2f5; height: 100%;">
                    <h2>🏠 SAP Fiori Launchpad</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-top: 20px;">
                        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <div style="font-size: 2em;">📦</div>
                            <strong>Gestión Materiales</strong>
                        </div>
                        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <div style="font-size: 2em;">🛒</div>
                            <strong>Ventas</strong>
                        </div>
                        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <div style="font-size: 2em;">💰</div>
                            <strong>Finanzas</strong>
                        </div>
                        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            <div style="font-size: 2em;">👥</div>
                            <strong>Recursos Humanos</strong>
                        </div>
                    </div>
                    <p style="margin-top: 20px; color: #666;">Sistema SAP S/4HANA - Cliente 100 - Usuario: <?php echo $user_info['sap_username'] ?? 'DEMO_USER'; ?></p>
                </div>
            `,
            'materiales': `
                <div style="padding: 20px; background: white; height: 100%;">
                    <h2>📦 Gestión de Materiales</h2>
                    <p>Módulo MM - Material Management</p>
                    <div style="background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0;">
                        <strong>Transacciones disponibles:</strong> MM01, MM02, MM03, MMBE, ME21N
                    </div>
                    <!-- Contenido simulado de SAP MM -->
                </div>
            `,
            'ventas': `
                <div style="padding: 20px; background: white; height: 100%;">
                    <h2>🛒 Ventas y Distribución</h2>
                    <p>Módulo SD - Sales and Distribution</p>
                    <div style="background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 15px 0;">
                        <strong>Transacciones disponibles:</strong> VA01, VA02, VA03, VK11, VK12
                    </div>
                </div>
            `,
            'finanzas': `
                <div style="padding: 20px; background: white; height: 100%;">
                    <h2>💰 Finanzas y Contabilidad</h2>
                    <p>Módulo FI - Financial Accounting</p>
                    <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;">
                        <strong>Transacciones disponibles:</strong> F-02, FB01, FB50, FS00, FBL3N
                    </div>
                </div>
            `
        };
        
        frame.innerHTML = moduleContents[module] || moduleContents['main'];
    }
    
    // Cargar módulo por defecto si hay sesión
    <?php if ($sap_session): ?>
    setTimeout(() => loadSAPModule('main'), 100);
    <?php endif; ?>
    </script>
</body>
</html>