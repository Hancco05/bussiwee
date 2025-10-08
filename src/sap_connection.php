<?php
class SAPConnection {
    private $conn;
    private $last_error;
    private $config;
    
    public function __construct() {
        $this->loadConfig();
    }
    
    private function loadConfig() {
        require_once '../config/db.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT * FROM config_sap WHERE activo = 1 ORDER BY id DESC LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $this->config = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function testConnection() {
        if (!$this->config) {
            $this->last_error = "Configuración SAP no encontrada o inactiva";
            return false;
        }
        
        // Validamos que los campos de configuración no estén vacíos
        if (empty($this->config['sap_server']) || empty($this->config['sap_company_db']) || 
            empty($this->config['sap_username']) || empty($this->config['sap_password'])) {
            $this->last_error = "Configuración SAP incompleta";
            return false;
        }
        
        // SIMULACIÓN: Para demo, siempre retornamos true si la configuración existe
        // En producción, aquí iría la lógica real de conexión a SAP
        $this->last_error = "Conexión simulada - Configuración válida detectada";
        return true;
    }
    
    public function sincronizarPedido($pedido_id) {
        if (!$this->testConnection()) {
            return false;
        }
        
        require_once '../config/db.php';
        $database = new Database();
        $db = $database->getConnection();
        
        // Obtener datos del pedido
        $query = "SELECT p.*, u.nombre as cliente, pr.nombre as producto, pr.sap_code 
                  FROM pedidos p 
                  JOIN usuarios u ON p.usuario_id = u.id 
                  JOIN productos pr ON p.producto_id = pr.id 
                  WHERE p.id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $pedido_id);
        $stmt->execute();
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pedido) {
            $this->last_error = "Pedido no encontrado";
            return false;
        }
        
        try {
            // SIMULACIÓN: Creación de pedido en SAP
            // En entorno real aquí iría: $this->conn->Connect() y lógica SAP
            
            // Generamos un número de documento SAP simulado
            $sap_doc_entry = "SO" . date('Ymd') . str_pad($pedido_id, 6, '0', STR_PAD_LEFT);
            
            // Registramos la sincronización
            $this->logSincronizacion("Pedido {$pedido_id} sincronizado como {$sap_doc_entry}");
            
            // Actualizar pedido con referencia SAP
            $query = "UPDATE pedidos SET sap_doc_entry = :sap_doc_entry, sincronizado_sap = 1 WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':sap_doc_entry', $sap_doc_entry);
            $stmt->bindParam(':id', $pedido_id);
            
            if ($stmt->execute()) {
                return $sap_doc_entry;
            } else {
                $this->last_error = "Error al actualizar pedido en base de datos local";
                return false;
            }
            
        } catch (Exception $e) {
            $this->last_error = "Error en sincronización: " . $e->getMessage();
            return false;
        }
    }
    
    public function sincronizarProductos() {
        if (!$this->testConnection()) {
            return false;
        }
        
        require_once '../config/db.php';
        $database = new Database();
        $db = $database->getConnection();
        
        try {
            // SIMULACIÓN: Obtención de productos desde SAP
            // En entorno real aquí iría la conexión real a SAP
            
            // Productos de ejemplo que vendrían de SAP
            $productos_sap = array(
                array(
                    'ItemCode' => 'SAP-' . date('Ymd') . '-001', 
                    'ItemName' => 'Laptop Gaming Professional', 
                    'Price' => 1350.00,
                    'Description' => 'Laptop para gaming y trabajo profesional - Sincronizado desde SAP'
                ),
                array(
                    'ItemCode' => 'SAP-' . date('Ymd') . '-002', 
                    'ItemName' => 'Smartphone Ultra', 
                    'Price' => 899.00,
                    'Description' => 'Smartphone última generación - Sincronizado desde SAP'
                ),
                array(
                    'ItemCode' => 'SAP-' . date('Ymd') . '-003', 
                    'ItemName' => 'Tablet Pro', 
                    'Price' => 499.00,
                    'Description' => 'Tablet profesional - Sincronizado desde SAP'
                )
            );
            
            $sincronizados = 0;
            
            foreach ($productos_sap as $producto_sap) {
                // Verificar si el producto ya existe por código SAP
                $query = "SELECT id FROM productos WHERE sap_code = :sap_code";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':sap_code', $producto_sap['ItemCode']);
                $stmt->execute();
                
                if ($stmt->rowCount() == 0) {
                    // Insertar nuevo producto
                    $query = "INSERT INTO productos (nombre, descripcion, precio, stock, sap_code, creado_por) 
                              VALUES (:nombre, :descripcion, :precio, :stock, :sap_code, 1)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':nombre', $producto_sap['ItemName']);
                    $stmt->bindParam(':descripcion', $producto_sap['Description']);
                    $stmt->bindParam(':precio', $producto_sap['Price']);
                    $stmt->bindValue(':stock', rand(5, 50));
                    $stmt->bindParam(':sap_code', $producto_sap['ItemCode']);
                    
                    if ($stmt->execute()) {
                        $sincronizados++;
                    }
                }
            }
            
            $this->logSincronizacion("Productos sincronizados: {$sincronizados} nuevos");
            
            return $sincronizados;
            
        } catch (Exception $e) {
            $this->last_error = "Error en sincronización de productos: " . $e->getMessage();
            return false;
        }
    }
    
    public function obtenerEstadoSAP() {
        if (!$this->config) {
            return array(
                'estado' => 'no_configurado',
                'mensaje' => 'Configuración SAP no encontrada'
            );
        }
        
        // Simulamos el estado del servidor SAP
        $estado = 'conectado'; // Para demo, siempre conectado
        
        return array(
            'estado' => 'conectado',
            'mensaje' => 'Conexión SAP simulada - Funcionando correctamente',
            'servidor' => $this->config['sap_server'],
            'base_datos' => $this->config['sap_company_db'],
            'ultima_sincronizacion' => date('Y-m-d H:i:s'),
            'modo' => 'SIMULACIÓN'
        );
    }
    
    private function logSincronizacion($mensaje) {
        // Guardar en archivo de log
        $log_file = '../logs/sap_sync.log';
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[{$timestamp}] {$mensaje}\n";
        
        // Crear directorio de logs si no existe
        if (!is_dir('../logs')) {
            mkdir('../logs', 0755, true);
        }
        
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    public function getLastError() {
        return $this->last_error;
    }
    
    public function getEstadisticasSincronizacion() {
        require_once '../config/db.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT 
                    COUNT(*) as total_pedidos,
                    SUM(sincronizado_sap) as pedidos_sincronizados,
                    (SELECT COUNT(*) FROM productos WHERE sap_code IS NOT NULL) as productos_con_sap
                  FROM pedidos";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>