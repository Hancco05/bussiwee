<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $pass = trim($_POST['password']);

    if (!empty($usuario) && !empty($pass)) {
        $passHash = password_hash($pass, PASSWORD_BCRYPT);

        $sql = "INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, 'usuario')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $usuario, $passHash);

        if ($stmt->execute()) {
            header("Location: index.php?registro=ok");
            exit;
        } else {
            $error = "? Error al registrar: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error = "?? Debes completar todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Registro</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<form method="post" action="">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button type="submit">Registrar</button>
    <p>¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a></p>
</form>

<?php if (isset($error)) : ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>
</body>
</html>
