<?php
class SAPWebConnection {
    private $config;
    private $last_error;
    private $session_data;
    
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
    
    /**
     * Login a SAP via Web (simulación realista)
     */
    public function loginToSAP($username, $password) {
        if (!$this->config) {
            $this->last_error = "Configuración SAP no encontrada";
            return false;
        }
        
        // Simulación de login real a SAP Web
        try {
            // En un entorno real, aquí usarías:
            // - SAP Fiori Launchpad
            // - SAP GUI for HTML
            // - SAP NetWeaver Gateway
            // - OData Services
            
            // Simulamos un retardo de red
            sleep(2);
            
            // Validación de credenciales (en realidad estas vendrían de SAP)
            $valid_credentials = [
                'demo_user' => 'demo123',
                'admin_sap' => 'admin123',
                'consultor' => 'consultor123'
            ];
            
            if (!isset($valid_credentials[$username]) || $valid_credentials[$username] !== $password) {
                $this->last_error = "Credenciales SAP incorrectas";
                return false;
            }
            
            // Generar sesión simulada
            $session_id = 'SAP_SESSION_' . uniqid() . '_' . time();
            
            $this->session_data = [
                'session_id' => $session_id,
                'username' => $username,
                'system' => $this->config['sap_system'] ?? 'S4HANA',
                'client' => $this->config['sap_client'] ?? '100',
                'language' => $this->config['sap_language'] ?? 'ES',
                'login_time' => date('Y-m-d H:i:s'),
                'expires' => date('Y-m-d H:i:s', strtotime('+8 hours'))
            ];
            
            // Guardar sesión en base de datos
            $this->saveSession($session_id);
            
            return $session_id;
            
        } catch (Exception $e) {
            $this->last_error = "Error en login SAP: " . $e->getMessage();
            return false;
        }
    }
    
    /**
     * Obtener datos básicos del usuario SAP
     */
    public function getUserInfo() {
        if (!$this->session_data) {
            $this->last_error = "No hay sesión SAP activa";
            return false;
        }
        
        // Simulación de datos de usuario desde SAP
        $user_profiles = [
            'demo_user' => [
                'name' => 'Usuario Demo SAP',
                'email' => 'demo_user@empresa.com',
                'role' => 'Usuario Final',
                'department' => 'Ventas',
                'company' => 'Empresa Demo S.A.',
                'sap_username' => 'DEMO_USER'
            ],
            'admin_sap' => [
                'name' => 'Administrador SAP',
                'email' => 'admin_sap@empresa.com',
                'role' => 'Administrador Basis',
                'department' => 'TI',
                'company' => 'Empresa Demo S.A.',
                'sap_username' => 'SAP_ADMIN'
            ],
            'consultor' => [
                'name' => 'Consultor Funcional',
                'email' => 'consultor@empresa.com',
                'role' => 'Consultor MM',
                'department' => 'Logística',
                'company' => 'Empresa Demo S.A.',
                'sap_username' => 'CONSULTOR_MM'
            ]
        ];
        
        $username = $this->session_data['username'];
        return $user_profiles[$username] ?? [
            'name' => $username,
            'email' => $username . '@empresa.com',
            'role' => 'Usuario SAP',
            'department' => 'Varios',
            'company' => 'Empresa Demo S.A.',
            'sap_username' => strtoupper($username)
        ];
    }
    
    /**
     * Abrir SAP Web en un iframe/ventana especial
     */
    public function getWebAccessURL($module = 'main') {
        if (!$this->config || !isset($this->config['sap_web_url'])) {
            $this->last_error = "URL web de SAP no configurada";
            return false;
        }
        
        $base_url = $this->config['sap_web_url'];
        $modules = [
            'main' => '/sap/bc/ui2/flp',
            'fiori' => '/sap/fiori/ui2',
            'gui' => '/sap/bc/gui/sap/its/webgui',
            'materiales' => '/sap/bc/ui5_ui5/ui2/ushell/shells/abap/Fiorilaunchpad.html?sap-client=100#Materiales-display',
            'ventas' => '/sap/bc/ui5_ui5/ui2/ushell/shells/abap/Fiorilaunchpad.html?sap-client=100#Ventas-display',
            'finanzas' => '/sap/bc/ui5_ui5/ui2/ushell/shells/abap/Fiorilaunchpad.html?sap-client=100#Finanzas-display'
        ];
        
        $module_path = $modules[$module] ?? $modules['main'];
        
        return $base_url . $module_path;
    }
    
    /**
     * Ejecutar transacción SAP específica
     */
    public function executeTransaction($transaction_code, $parameters = []) {
        if (!$this->session_data) {
            $this->last_error = "Sesión SAP no activa";
            return false;
        }
        
        // Simulación de ejecución de transacción SAP
        $transactions = [
            'MM01' => 'Crear Material',
            'VA01' => 'Crear Pedido de Venta',
            'ME21N' => 'Crear Orden de Compra',
            'F-02' => 'Contabilizar Documento',
            'MIRO' => 'Ingresar Factura'
        ];
        
        if (!isset($transactions[$transaction_code])) {
            $this->last_error = "Transacción {$transaction_code} no válida";
            return false;
        }
        
        // Simular ejecución
        sleep(1);
        
        return [
            'success' => true,
            'transaction' => $transaction_code,
            'description' => $transactions[$transaction_code],
            'execution_time' => date('Y-m-d H:i:s'),
            'message' => "Transacción {$transaction_code} ejecutada exitosamente",
            'document_number' => 'DOC_' . date('Ymd') . '_' . rand(1000, 9999)
        ];
    }
    
    /**
     * Obtener datos desde SAP via OData (simulación)
     */
    public function getOData($endpoint, $filters = []) {
        if (!$this->session_data) {
            $this->last_error = "Sesión SAP no activa";
            return false;
        }
        
        // Simulación de endpoints OData de SAP
        $endpoints = [
            'Materials' => [
                ['Material' => 'MAT-001', 'Description' => 'Laptop Gaming', 'Price' => 1200.00],
                ['Material' => 'MAT-002', 'Description' => 'Smartphone', 'Price' => 800.00],
                ['Material' => 'MAT-003', 'Description' => 'Tablet', 'Price' => 500.00]
            ],
            'SalesOrders' => [
                ['Order' => 'SO-001', 'Customer' => 'Cliente A', 'Total' => 2400.00],
                ['Order' => 'SO-002', 'Customer' => 'Cliente B', 'Total' => 800.00]
            ],
            'Customers' => [
                ['Customer' => 'CUST-001', 'Name' => 'Empresa ABC', 'City' => 'Madrid'],
                ['Customer' => 'CUST-002', 'Name' => 'Compañía XYZ', 'City' => 'Barcelona']
            ]
        ];
        
        return $endpoints[$endpoint] ?? [];
    }
    
    private function saveSession($session_id) {
        require_once '../config/db.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $user_id = $_SESSION['user_id'] ?? 1;
        $expiration = date('Y-m-d H:i:s', strtotime('+8 hours'));
        
        $query = "INSERT INTO sap_web_sessions (usuario_id, sap_session_id, sap_server_url, logged_in, fecha_expiracion) 
                  VALUES (:usuario_id, :session_id, :server_url, 1, :expiration)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':usuario_id', $user_id);
        $stmt->bindParam(':session_id', $session_id);
        $stmt->bindParam(':server_url', $this->config['sap_web_url']);
        $stmt->bindParam(':expiration', $expiration);
        
        return $stmt->execute();
    }
    
    public function getLastError() {
        return $this->last_error;
    }
    
    public function getSessionData() {
        return $this->session_data;
    }
}
?>