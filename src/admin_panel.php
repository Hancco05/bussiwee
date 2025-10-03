<?php
session_start();
include '../config/db.php';

// Validar que el usuario logueado sea admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../public/index.php");
    exit;
}

// Crear usuario
if (isset($_POST['crear'])) {
    $usuario = $_POST['usuario'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $usuario, $password, $rol);

    if ($stmt->execute()) {
        echo "✅ Usuario creado correctamente.<br>";
    } else {
        echo "❌ Error: " . $conn->error . "<br>";
    }
}

// Actualizar usuario
if (isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $usuario = $_POST['usuario'];
    $rol = $_POST['rol'];

    $stmt = $conn->prepare("UPDATE usuarios SET usuario=?, rol=? WHERE id=?");
    $stmt->bind_param("ssi", $usuario, $rol, $id);

    if ($stmt->execute()) {
        echo "✅ Usuario actualizado correctamente.<br>";
    } else {
        echo "❌ Error: " . $conn->error . "<br>";
    }
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "✅ Usuario eliminado correctamente.<br>";
    } else {
        echo "❌ Error: " . $conn->error . "<br>";
    }
}

// Obtener lista de usuarios
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
    <a href="../public/index.php">⬅️ Volver al Login</a> | 
    <a href="logout.php">🚪 Cerrar Sesión</a>

    <h2>Crear Usuario</h2>
    <form method="post">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <select name="rol">
            <option value="usuario">Usuario</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit" name="crear">Crear</button>
    </form>

    <h2>Usuarios Registrados</h2>
    <table>
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
                    <td><input type="text" name="usuario" value="<?php echo $row['usuario']; ?>"></td>
                    <td>
                        <select name="rol">
                            <option value="usuario" <?php if ($row['rol'] === 'usuario') echo "selected"; ?>>Usuario</option>
                            <option value="admin" <?php if ($row['rol'] === 'admin') echo "selected"; ?>>Admin</option>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="actualizar">Actualizar</button>
                        <a href="admin_panel.php?eliminar=<?php echo $row['id']; ?>" onclick="return confirm('¿Eliminar este usuario?');">Eliminar</a>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
