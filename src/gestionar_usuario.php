<?php
session_start();
include '../config/db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../public/index.php");
    exit;
}

// Verificar si es admin
if ($_SESSION['usuario_rol'] !== 'admin') {
    echo "⛔ Acceso denegado. No tienes permisos para entrar aquí.";
    exit;
}

// Eliminar usuario
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Evitar que el admin se borre a sí mismo
    if ($id == $_SESSION['usuario_id']) {
        echo "❌ No puedes eliminar tu propia cuenta de administrador.<br>";
    } else {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "✅ Usuario eliminado correctamente.<br>";
        } else {
            echo "❌ Error al eliminar usuario: " . $conn->error . "<br>";
        }
    }
}

// Cambiar rol (admin <-> usuario)
if (isset($_GET['cambiar_rol'])) {
    $id = intval($_GET['cambiar_rol']);

    // Evitar que el admin cambie su propio rol
    if ($id == $_SESSION['usuario_id']) {
        echo "⚠️ No puedes cambiar tu propio rol.<br>";
    } else {
        // Obtener rol actual
        $sql = "SELECT rol FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $nuevoRol = ($row['rol'] === 'admin') ? 'usuario' : 'admin';

            // Actualizar rol
            $sql = "UPDATE usuarios SET rol = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $nuevoRol, $id);

            if ($stmt->execute()) {
                echo "✅ Rol cambiado a <b>$nuevoRol</b> para el usuario con ID $id.<br>";
            } else {
                echo "❌ Error al cambiar rol: " . $conn->error . "<br>";
            }
        }
    }
}

// Obtener todos los usuarios
$sql = "SELECT id, usuario, rol FROM usuarios";
$result = $conn->query($sql);
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
            <th>ID</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['usuario'], ENT_QUOTES, 'ISO-8859-1'); ?></td>
            <td><?php echo $row['rol']; ?></td>
            <td>
                <?php if ($row['id'] != $_SESSION['usuario_id']): ?>
                    <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('¿Seguro que quieres eliminar este usuario?')">❌ Eliminar</a> | 
                    <a href="?cambiar_rol=<?php echo $row['id']; ?>" onclick="return confirm('¿Quieres cambiar el rol de este usuario?')">🔄 Cambiar Rol</a>
                <?php else: ?>
                    (No puedes eliminarte ni cambiar tu propio rol)
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="admin_panel.php">⬅️ Volver al Panel de Administración</a>
</div>

</body>
</html>
