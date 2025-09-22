<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    // Redirigir según rol
    if ($_SESSION['usuario_rol'] === 'admin') {
        header("Location: ../src/admin_panel.php");
    } else {
        header("Location: ../src/dashboard.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <form method="post" action="../src/login.php">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Ingresar</button>
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>

        <?php
        if (isset($_GET['registro']) && $_GET['registro'] === 'ok') {
            echo "<p style='color:green;'>Usuario creado con éxito. Ahora inicia sesión.</p>";
        }
        if (isset($_GET['error'])) {
            echo "<p style='color:red;'>".$_GET['error']."</p>";
        }
        ?>
    </form>
</body>
</html>
