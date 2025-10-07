<?php
session_start();
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $pass = $_POST['password'];

    // Caso especial: admin hardcodeado
    if ($usuario === "admin" && $pass === "1234") {
        $_SESSION['usuario_id'] = 0;
        $_SESSION['usuario_nombre'] = "admin";
        $_SESSION['usuario_rol'] = "admin";
        header("Location: admin_panel.php");
        exit;
    }

    // Caso normal: usuario desde BD
    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($pass, $row['password'])) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['usuario_nombre'] = $row['usuario'];
            $_SESSION['usuario_rol'] = $row['rol'];

            if ($row['rol'] === "admin") {
                header("Location: admin_panel.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            echo "? Contraseña incorrecta";
        }
    } else {
        echo "? Usuario no encontrado";
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
<div class="form-container">
    <h2>Login</h2>
    <form method="post">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Ingresar</button>
    </form>
    <p>¿No tienes cuenta? <a href="../src/register.php">Regístrate aquí</a></p>
</div>
</body>
</html>
