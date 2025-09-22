<?php
require_once "../config/config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    if (!empty($usuario) && !empty($password)) {
        // Encriptar la contraseña
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Insertar en la BD
        $sql = "INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, 'usuario')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $usuario, $passwordHash);

        if ($stmt->execute()) {
            // ? Redirigir al login
            header("Location: ../public/index.php?registro=ok");
            exit;
        } else {
            $error = "Error al registrar usuario: " . $conn->error;
        }

        $stmt->close();
    } else {
        $error = "Debes llenar todos los campos.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Registro</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <form method="post" action="registro.php">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrarse</button>
        <p>¿Ya tienes cuenta? <a href="../public/index.php">Inicia sesión aquí</a></p>
    </form>

    <?php if (isset($error)) : ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
</body>
</html>
