<?php
session_start();

// Verificar que sea admin
if (!isset($_SESSION['usuario_nombre']) || $_SESSION['usuario_nombre'] !== 'admin') {
    header("Location: ../public/index.php");
    exit;
}

include '../config/db.php';

// --- Crear usuario ---
if (isset($_POST['crear'])) {
    $usuario = $_POST['usuario'];
    $pass = $_POST['contraseña'];
    $rol = $_POST['rol'];

    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, contraseña, rol) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $usuario, $hash, $rol);

    if ($stmt->execute()) {
        $msg = "✅ Usuario creado con éxito.";
    } else {
        $msg = "❌ Error al crear usuario: " . $conn->error;
    }
}

// --- Eliminar usuario ---
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM usuarios WHERE id = $id");
    $msg = "🗑️ Usuario eliminado.";
}

// --- Actualizar usuario ---
if (isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $usuario = $_POST['usuario'];
    $rol = $_POST['rol'];

    $stmt = $conn->prepare("UPDATE usuarios SET usuario=?, rol=? WHERE id=?");
    $stmt->bind_param("ssi", $usuario, $rol, $id);

    if ($stmt->execute()) {
        $msg = "✏️ Usuario actualizado.";
    } else {
        $msg = "❌ Error al actualizar.";
    }
}

// --- Obtener lista de usuarios ---
$result = $conn->query("SELECT * FROM usuarios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="dashboard-container">
    <h1>🔑 Panel de Administración</h1>
    <p><?php echo isset($msg) ? $msg : ""; ?></p>

    <!-- Formulario para crear usuario -->
    <h2>➕ Crear Usuario</h2>
    <form method="post">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="contraseña" placeholder="Contraseña" required>
        <select name="rol">
            <option value="user">Usuario</option>
            <option value="admin">Administrador</option>
        </select>
        <button type="submit" name="crear">Crear</button>
    </form>

    <hr>

    <!-- Listado de usuarios -->
    <h2>📋 Lista de Usuarios</h2>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <form method="post">
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <input type="text" name="usuario" value="<?php echo $row['usuario']; ?>">
                    </td>
                    <td>
                        <select name="rol">
                            <option value="user" <?php if ($row['rol'] === 'user') echo "selected"; ?>>Usuario</option>
                            <option value="admin" <?php if ($row['rol'] === 'admin') echo "selected"; ?>>Administrador</option>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="actualizar">Actualizar</button>
                        <a href="admin_panel.php?eliminar=<?php echo $row['id']; ?>" onclick="return confirm('¿Eliminar usuario?');">Eliminar</a>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="../public/index.php">🚪 Cerrar sesión</a>
</div>
</body>
</html>
