<?php
session_start();
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $pass = $_POST['contrasena'];

    // Caso especial: admin hardcodeado
    if ($usuario === "admin" && $pass === "1234") {
        $_SESSION['usuario_id'] = 0;
        $_SESSION['usuario_nombre'] = "admin";
        $_SESSION['usuario_rol'] = "admin";
        header("Location: ../src/admin_panel.php");
        exit;
    }

    // Buscar usuario en la BD
    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($pass, $row['contrasena'])) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['usuario_nombre'] = $row['usuario'];
            $_SESSION['usuario_rol'] = $row['rol'];

            if ($row['rol'] === "admin") {
                header("Location: ../src/admin_panel.php");
            } else {
                header("Location: ../src/dashboard.php");
            }
            exit;
        } else {
            $error = "? Contraseña incorrecta";
        }
    } else {
        $error = "? Usuario no encontrado";
    }
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
<form method="post" action="">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contrasena" placeholder="Contraseña" required>
    <button type="submit">Ingresar</button>
    <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
</form>

<?php if (isset($error)) : ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>
</body>
</html>
