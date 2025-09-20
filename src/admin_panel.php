<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'admin') {
    header("Location: ../public/index.php");
    exit;
}
include '../config/db.php';

// Cambiar rol
if (isset($_POST['cambiar_rol'])) {
    $usuario_id = $_POST['usuario_id'];
    $nuevo_rol = $_POST['nuevo_rol'];
    $stmt = $conn->prepare("UPDATE usuarios SET rol=? WHERE id=?");
    $stmt->bind_param("si", $nuevo_rol, $usuario_id);
    $stmt->execute();
}

// Listar usuarios
$result = $conn->query("SELECT id, usuario, rol FROM usuarios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Panel Admin</title>
</head>
<body>
<h1>Panel de Administración</h1>
<table border="1">
<tr><th>ID</th><th>Usuario</th><th>Rol</th><th>Acción</th></tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlspecialchars($row['usuario'], ENT_QUOTES, 'ISO-8859-1'); ?></td>
    <td><?php echo $row['rol']; ?></td>
    <td>
        <form method="post">
            <input type="hidden" name="usuario_id" value="<?php echo $row['id']; ?>">
            <select name="nuevo_rol">
                <option value="usuario" <?php if($row['rol']=='usuario') echo 'selected'; ?>>Usuario</option>
                <option value="admin" <?php if($row['rol']=='admin') echo 'selected'; ?>>Admin</option>
            </select>
            <button type="submit" name="cambiar_rol">Cambiar rol</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>

<a href="../public/index.php">Cerrar sesión</a>
</body>
</html>

