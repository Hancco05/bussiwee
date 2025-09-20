<?php
include '../config/db.php';

echo "<meta charset='ISO-8859-1'>";
echo "<h1>Verificación de usuarios</h1>";

$sql = "SELECT id, usuario, contraseña, rol FROM usuarios";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Rol</th><th>Hash</th><th>Prueba '1234'</th></tr>";
    while($row = $result->fetch_assoc()) {
        $coincide = password_verify('1234', $row['contraseña']) ? "✅ Coincide" : "❌ No coincide";
        echo "<tr>";
        echo "<td>".$row['id']."</td>";
        echo "<td>".htmlspecialchars($row['usuario'], ENT_QUOTES, 'ISO-8859-1')."</td>";
        echo "<td>".$row['rol']."</td>";
        echo "<td>".$row['contraseña']."</td>";
        echo "<td>".$coincide."</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No hay usuarios en la base de datos.";
}
?>
