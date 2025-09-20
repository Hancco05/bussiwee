<?php
session_start();
include '../config/db.php';

// Solo accesible para admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../public/index.php");
    exit;
}

// Mensaje para feedback
$mensaje = "";

// ------------------ CREAR USUARIO ------------------
if (isset($_POST['crear_usuario'])) {
    $usuario = $_POST['usuario'];
    $pass = $_POST['contraseña'];
    $rol = $_POST['rol'];
    $passHash = password_hash($pass, PASSWORD_DEFAULT);

    // Verificar si ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario=?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, contraseña, rol) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $usuario, $passHash, $rol);
        $stmt->execute();
        $mensaje = "✅ Usuario '$usuario' creado correctamente.";
    } else {
        $mensaje = "⚠️ El usuario '$usuario' ya existe.";
    }
}

// ------------------ ACTUALIZAR USUARIO ------------------
if (isset($_POST['actualizar_usuario'])) {
    $id = $_POST['usuario_id'];
    $usuario = $_POST['usuario'];
    $rol = $_POST['rol'];

    if (!empty($_POST['contraseña'])) {
        $passHash = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET usuario=?, contraseña=?, rol=? WHERE id=?");
        $stmt->bind_param("sssi", $usuario, $passHash, $rol, $id);
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET usuario=?, rol=? WHERE id=?");
        $stmt->bind_param("ssi", $usuario, $rol, $id);
    }

    $stmt->execute();
    $mensaje = "✅ Usuario actualizado correctamente.";
}

// ------------------ ELIMINAR USUARIO ------------------
if (isset($_POST['eliminar_usuario'])) {
    $id = $_POST['usuario_id'];
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $mensaje = "✅ Usuario eliminado correctamente.";
}

// ------------------ LISTAR USUARIOS ------------------
$result = $conn->query("SELECT id, usuario, rol FROM usuarios ORDER BY id ASC");
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
    <h1>Panel de Administración</h1>

    <?php if($mensaje) echo "<p>$mensaje</p>"; ?>

    <!-- FORMULARIO CREAR USUARIO -->
    <h2>Crear Usuario</h2>
    <form method="post">
        <input type="text" name="usuario" placeholder="Nombre de usuario" required>
        <input type="password" name="contraseña" placeholder="Contraseña" required>
        <select name="rol">
            <option value="usuario">Usuario</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit" name="crear_usuario">Crear Usuario</button>
    </form>

    <h2>Lista de Usuarios</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['usuario'], ENT_QUOTES, 'ISO-8859-1'); ?></td>
            <td><?php echo $row['rol']; ?></td>
            <td>
                <!-- Formulario para actualizar -->
                <form method="post" style="display:inline-block;">
                    <input type="hidden" name="usuario_id" value="<?php echo $row['id']; ?>">
                    <input type="text" name="usuario" value="<?php echo htmlspecialchars($row['usuario'], ENT_QUOTES, 'ISO-8859-1'); ?>" required>
                    <input type="password" name="contraseña" placeholder="Nueva contraseña">
                    <select name="rol">
                        <option value="usuario" <?php if($row['rol']=='usuario') echo 'selected'; ?>>Usuario</option>
                        <option value="admin" <?php if($row['rol']=='admin') echo 'selected'; ?>>Admin</option>
                    </select>
                    <button type="submit" name="actualizar_usuario">Actualizar</button>
                </form>

                <!-- Formulario para eliminar -->
                <form method="post" style="display:inline-block;">
                    <input type="hidden" name="usuario_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="eliminar_usuario" onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">Eliminar</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="../public/index.php">Cerrar sesión</a></p>
</div>
</body>
</html>
