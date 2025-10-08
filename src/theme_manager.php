<?php
class ThemeManager {
    private $db;
    private $usuario_id;
    private $theme_config;
    
    public function __construct($usuario_id) {
        require_once '../config/db.php';
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario_id = $usuario_id;
        $this->loadThemeConfig();
    }
    
    private function loadThemeConfig() {
        $query = "SELECT * FROM config_theme WHERE usuario_id = :usuario_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':usuario_id', $this->usuario_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $this->theme_config = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Configuración por defecto
            $this->theme_config = [
                'tema' => 'claro',
                'color_primario' => '#007bff',
                'densidad' => 'comodo'
            ];
            $this->createDefaultConfig();
        }
    }
    
    private function createDefaultConfig() {
        $query = "INSERT INTO config_theme (usuario_id, tema, color_primario, densidad) 
                  VALUES (:usuario_id, 'claro', '#007bff', 'comodo')";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':usuario_id', $this->usuario_id);
        $stmt->execute();
    }
    
    public function updateTheme($tema, $color_primario = null, $densidad = null) {
        // Usar valores por defecto si no se proporcionan
        $color_final = ($color_primario !== null) ? $color_primario : $this->theme_config['color_primario'];
        $densidad_final = ($densidad !== null) ? $densidad : $this->theme_config['densidad'];
        
        $query = "UPDATE config_theme SET 
                  tema = :tema,
                  color_primario = :color_primario,
                  densidad = :densidad,
                  fecha_actualizacion = CURRENT_TIMESTAMP
                  WHERE usuario_id = :usuario_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tema', $tema);
        $stmt->bindParam(':color_primario', $color_final);
        $stmt->bindParam(':densidad', $densidad_final);
        $stmt->bindParam(':usuario_id', $this->usuario_id);
        
        if ($stmt->execute()) {
            $this->loadThemeConfig(); // Recargar configuración
            return true;
        }
        return false;
    }
    
    public function getThemeClass() {
        return 'theme-' . $this->theme_config['tema'] . ' density-' . $this->theme_config['densidad'];
    }
    
    public function getCSSVariables() {
        $colors = $this->getColorScheme();
        $css = ":root {\n";
        
        foreach ($colors as $variable => $value) {
            $css .= "  --{$variable}: {$value};\n";
        }
        
        $css .= "}\n";
        return $css;
    }
    
    private function getColorScheme() {
        if ($this->theme_config['tema'] === 'oscuro') {
            return [
                'bg-primary' => '#1a1a1a',
                'bg-secondary' => '#2d2d2d',
                'bg-card' => '#3d3d3d',
                'text-primary' => '#ffffff',
                'text-secondary' => '#b0b0b0',
                'text-muted' => '#888888',
                'border-color' => '#444444',
                'shadow-color' => 'rgba(0,0,0,0.3)',
                'accent-color' => $this->theme_config['color_primario'],
                'success-color' => '#28a745',
                'warning-color' => '#ffc107',
                'error-color' => '#dc3545',
                'info-color' => '#17a2b8'
            ];
        } else {
            return [
                'bg-primary' => '#ffffff',
                'bg-secondary' => '#f8f9fa',
                'bg-card' => '#ffffff',
                'text-primary' => '#333333',
                'text-secondary' => '#666666',
                'text-muted' => '#888888',
                'border-color' => '#dee2e6',
                'shadow-color' => 'rgba(0,0,0,0.1)',
                'accent-color' => $this->theme_config['color_primario'],
                'success-color' => '#28a745',
                'warning-color' => '#ffc107',
                'error-color' => '#dc3545',
                'info-color' => '#17a2b8'
            ];
        }
    }
    
    public function getThemeConfig() {
        return $this->theme_config;
    }
    
    public function isDarkMode() {
        return $this->theme_config['tema'] === 'oscuro';
    }
    
    // Método para aplicar el tema automáticamente en cada página
    public function applyTheme() {
        $themeClass = $this->getThemeClass();
        $cssVariables = $this->getCSSVariables();
        
        // Retornar el HTML para incluir en el head
        return "
        <style>
        {$cssVariables}
        body.{$themeClass} {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }
        </style>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.className = '{$themeClass}';
        });
        </script>";
    }
}
?>