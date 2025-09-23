<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../public/index.php");
    exit;
}

// Crear usuario
if (isset($_POST['crear'])) {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['contraseña']);
    $rol = $_POST['rol'];

    if ($usuario && $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO usuarios (usuario, contraseña, rol) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $usuario, $hash, $rol);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_panel.php");
    exit;
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_panel.php");
    exit;
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
<div class="dashboard-container">
    <h1>Panel de Administración 🔑</h1>

    <h2>Crear Usuario</h2>
    <form method="post" action="">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="contraseña" placeholder="Contraseña" required>
        <select name="rol">
            <option value="usuario">Usuario</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit" name="crear">Crear</button>
    </form>

    <h2>Lista de Usuarios</h2>
    <table border="1" cellpadding="5">
        <tr><th>ID</th><th>Usuario</th><th>Rol</th><th>Acciones</th></tr>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                <td><?php echo $row['rol']; ?></td>
                <td><a href="?eliminar=<?php echo $row['id']; ?>">Eliminar</a></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="../public/index.php">🔙 Volver al Login</a>
</div>
</body>
</html>
