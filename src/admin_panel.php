<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../public/index.php?error=Acceso no autorizado");
    exit;
}

// Crear usuario
if (isset($_POST['accion']) && $_POST['accion'] === "crear") {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $rol = $_POST['rol'];

    if (!empty($usuario) && !empty($password)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $usuario, $hash, $rol);
        $stmt->execute();
    }
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM usuarios WHERE id = $id");
}

// Modificar usuario
if (isset($_POST['accion']) && $_POST['accion'] === "editar") {
    $id = intval($_POST['id']);
    $rol = $_POST['rol'];

    $sql = "UPDATE usuarios SET rol = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $rol, $id);
    $stmt->execute();
}

// Listar usuarios
$result = $conn->query("SELECT id, usuario, rol FROM usuarios");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<h1>Panel de Administración</h1>

<h2>Crear Usuario</h2>
<form method="post">
    <input type="hidden" name="accion" value="crear">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <select name="rol">
        <option value="usuario">Usuario</option>
        <option value="admin">Administrador</option>
    </select>
    <button type="submit">Crear</button>
</form>

<h2>Lista de Usuarios</h2>
<table border="1" style="margin:auto; border-collapse:collapse; width:70%;">
    <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Rol</th>
        <th>Acciones</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['usuario']); ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <select name="rol">
                        <option value="usuario" <?php if($row['rol']=="usuario") echo "selected"; ?>>Usuario</option>
                        <option value="admin" <?php if($row['rol']=="admin") echo "selected"; ?>>Administrador</option>
                    </select>
                    <button type="submit">Actualizar</button>
                </form>
            </td>
            <td>
                <a href="?eliminar=<?php echo $row['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">Eliminar</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="../public/dashboard.php">Volver al Dashboard</a>
</body>
</html>
