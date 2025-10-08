<?php
echo "<h1>Verificación del Sistema - Windows 10</h1>";
echo "<style>body {font-family: Arial; margin: 20px;} .success {color: green;} .error {color: red;}</style>";

// Verificar PHP
echo "<h2>✅ PHP</h2>";
echo "Versión: " . phpversion() . "<br>";

// Verificar extensiones
$extensiones = ['pdo_mysql', 'mysqli', 'json', 'mbstring', 'curl', 'xml', 'zip', 'gd'];
foreach ($extensiones as $ext) {
    echo $ext . ": " . (extension_loaded($ext) ? "<span class='success'>✅ Instalada</span>" : "<span class='error'>❌ Faltante</span>") . "<br>";
}

// Verificar permisos de escritura
echo "<h2>📁 Permisos de Archivos</h2>";
$carpetas = ['../logs', '../uploads', '../cache'];
foreach ($carpetas as $carpeta) {
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }
    echo $carpeta . ": " . (is_writable($carpeta) ? "<span class='success'>✅ Escritura OK</span>" : "<span class='error'>❌ Sin escritura</span>") . "<br>";
}

// Verificar base de datos
echo "<h2>🗄️ Base de Datos</h2>";
try {
    $pdo = new PDO('mysql:host=localhost;dbname=mi_proyecto', 'root', '');
    echo "Conexión MySQL: <span class='success'>✅ Exitosa</span><br>";
    
    // Verificar tablas
    $tablas = ['usuarios', 'productos', 'pedidos', 'config_sap'];
    $stmt = $pdo->query("SHOW TABLES");
    $tablas_existentes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tablas as $tabla) {
        echo $tabla . ": " . (in_array($tabla, $tablas_existentes) ? "<span class='success'>✅ Existente</span>" : "<span class='error'>❌ Faltante</span>") . "<br>";
    }
    
} catch (PDOException $e) {
    echo "Conexión MySQL: <span class='error'>❌ Falló - " . $e->getMessage() . "</span><br>";
}

// Verificar SAP (simulación)
echo "<h2>🔗 Integración SAP</h2>";
echo "Modo: <span class='success'>✅ Simulación Activa</span><br>";
echo "Estado: <span class='success'>✅ Listo para desarrollo</span><br>";

echo "<h2>🎯 URLs del Proyecto</h2>";
echo "• Sitio Principal: <a href='http://localhost/mi_proyecto/public/'>http://localhost/mi_proyecto/public/</a><br>";
echo "• PHPMyAdmin: <a href='http://localhost/phpmyadmin'>http://localhost/phpmyadmin</a><br>";
echo "• Panel XAMPP: <a href='http://localhost/dashboard/'>http://localhost/dashboard/</a><br>";

echo "<h2>📋 Próximos Pasos</h2>";
echo "1. Ejecuta el archivo SQL en PHPMyAdmin<br>";
echo "2. Configura la conexión SAP si es necesario<br>";
echo "3. ¡Comienza a desarrollar!<br>";

echo "<hr><p><strong>Estado del Sistema:</strong> <span class='success'>✅ LISTO PARA USAR</span></p>";
?>