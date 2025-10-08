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
        
        try {
            // Para SAP Business One DI API (COM object en Windows)
            if (class_exists('COM')) {
                $this->conn = new COM("SAPbobsCOM.company");
                $this->conn->Server = $this->config['sap_server'];
                $this->conn->CompanyDB = $this->config['sap_company_db'];
                $this->conn->UserName = $this->config['sap_username'];
                $this->conn->Password = $this->config['sap_password'];
                $this->conn->DbServerType = 9; // MSSQL
                
                $result = $this->conn->Connect();
                
                if ($result != 0) {
                    $this->last_error = "Error conectando a SAP: " . $this->getSAPError($result);
                    return false;
                }
                
                $this->conn->Disconnect();
                return true;
            } else {
                // Alternativa usando SAP RFC (sapnwrfc)
                if (function_exists('sapnwrfc_open')) {
                    $config = array(
                        'ASHOST' => $this->config['sap_server'],
                        'SYSNR'  => '00',
                        'CLIENT' => '100',
                        'USER'   => $this->config['sap_username'],
                        'PASSWD' => $this->config['sap_password'],
                        'LANG'   => 'ES'
                    );
                    
                    $this->conn = sapnwrfc_open($config);
                    
                    if (!$this->conn) {
                        $this->last_error = "Error conectando vía RFC";
                        return false;
                    }
                    
                    sapnwrfc_close($this->conn);
                    return true;
                } else {
                    $this->last_error = "Extension SAP no disponible. Simulando conexión exitosa para demo.";
                    // Para demo, retornamos true
                    return true;
                }
            }
        } catch (Exception $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
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
            // Simulación de creación de pedido en SAP
            // En un entorno real, aquí iría la lógica para crear el pedido en SAP
            $sap_doc_entry = "SO" . date('Ymd') . str_pad($pedido_id, 6, '0', STR_PAD_LEFT);
            
            // Actualizar pedido con referencia SAP
            $query = "UPDATE pedidos SET sap_doc_entry = :sap_doc_entry, sincronizado_sap = 1 WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':sap_doc_entry', $sap_doc_entry);
            $stmt->bindParam(':id', $pedido_id);
            
            if ($stmt->execute()) {
                return $sap_doc_entry;
            } else {
                $this->last_error = "Error al actualizar pedido";
                return false;
            }
            
        } catch (Exception $e) {
            $this->last_error = $e->getMessage();
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
            // Simulación de obtención de productos desde SAP
            // En un entorno real, aquí iría la lógica para obtener productos de SAP
            $productos_sap = array(
                array('ItemCode' => 'P001', 'ItemName' => 'Laptop Gaming SAP', 'Price' => 1250.00),
                array('ItemCode' => 'P002', 'ItemName' => 'Smartphone SAP', 'Price' => 850.00),
                array('ItemCode' => 'P003', 'ItemName' => 'Tablet SAP', 'Price' => 450.00)
            );
            
            $sincronizados = 0;
            foreach ($productos_sap as $producto_sap) {
                // Verificar si el producto ya existe
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
                    $stmt->bindParam(':descripcion', $producto_sap['ItemName']);
                    $stmt->bindParam(':precio', $producto_sap['Price']);
                    $stmt->bindValue(':stock', 10);
                    $stmt->bindParam(':sap_code', $producto_sap['ItemCode']);
                    $stmt->execute();
                    $sincronizados++;
                }
            }
            
            return $sincronizados;
            
        } catch (Exception $e) {
            $this->last_error = $e->getMessage();
            return false;
        }
    }
    
    private function getSAPError($error_code) {
        $errors = array(
            -1 => "Error general",
            -2 => "Base de datos no encontrada",
            -3 => "Usuario o contraseña incorrectos",
            -4 => "Servidor no encontrado"
        );
        
        return isset($errors[$error_code]) ? $errors[$error_code] : "Error desconocido: " . $error_code;
    }
    
    public function getLastError() {
        return $this->last_error;
    }
}
?>