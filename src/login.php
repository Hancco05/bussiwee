<?php
session_start();
require_once __DIR__ . "/../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"]);
    $password = trim($_POST["password"]);

    // Caso especial: admin "hardcodeado"
    if ($usuario === "admin" && $password === "1234") {
        $_SESSION["usuario"] = "admin";
        $_SESSION["rol"] = "admin";
        header("Location: ../src/admin_panel.php");
        exit();
    }

    // Caso normal: validar usuario en la base de datos
    $query = "SELECT id, usuario, contraseña, rol FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row["contraseña"])) {
            $_SESSION["usuario"] = $row["usuario"];
            $_SESSION["rol"] = $row["rol"] ?? "usuario";
            header("Location: ../src/dashboard.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post" action="">
        <label>Usuario:</label>
        <input type="text" name="usuario" required><br><br>
        <label>Contraseña:</label>
        <input type="password" name="password" required><br><br>
        <button type="submit">Ingresar</button>
    </form>
    <br>
    <a href="../src/register.php">Registrarse</a>
</body>
</html>
