<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    echo "⛔ Acceso denegado.";
    exit;
}

// Eliminar usuario
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id != $_SESSION['usuario_id']) {
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo "✅ Usuario eliminado.<br>";
    } else {
        echo "⚠️ No puedes eliminar tu propia cuenta.<br>";
    }
}

// Cambiar rol
if (isset($_GET['cambiar_rol'])) {
    $id = intval($_GET['cambiar_rol']);
    if ($id != $_SESSION['usuario_id']) {
        $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row) {
            $nuevoRol = ($row['rol']==='admin')?'usuario':'admin';
            $stmt2 = $conn->prepare("UPDATE usuarios SET rol=? WHERE id=?");
            $stmt2->bind_param("si", $nuevoRol, $id);
            $stmt2->execute();
            echo "✅ Rol cambiado a $nuevoRol.<br>";
        }
    } else {
        echo "⚠️ No puedes cambiar tu propio rol.<br>";
    }
}

// Obtener usuarios
$result = $conn->query("SELECT id, usuario, rol FROM usuarios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Gestionar Usuarios</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="admin-container">
    <h1>👥 Gestionar Usuarios</h1>
    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th><th>Usuario</th><th>Rol</th><th>Acciones</th>
        </tr>
        <?php while($row=$result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id'];?></td>
            <td><?php echo htmlspecialchars($row['usuario'], ENT_QUOTES,'ISO-8859-1');?></td>
            <td><?php echo $row['rol'];?></td>
            <td>
                <?php if($row['id'] != $_SESSION['usuario_id']): ?>
                    <a href="?delete=<?php echo $row['id'];?>" onclick="return confirm('Eliminar usuario?')">❌ Eliminar</a> |
                    <a href="?cambiar_rol=<?php echo $row['id'];?>" onclick="return confirm('Cambiar rol?')">🔄 Cambiar Rol</a>
                <?php else: ?>
                    (No puedes eliminarte ni cambiar tu propio rol)
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile;?>
    </table>
    <br>
    <a href="admin_panel.php">⬅️ Volver al Panel de Administraci&oacute;n</a>
</div>

</body>
</html>
