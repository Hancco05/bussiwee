<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $pass = $_POST['password'];

    // Generar hash seguro
    $passHash = password_hash($pass, PASSWORD_DEFAULT);

    // Insertar usuario en la BD con rol por defecto = usuario
    $sql = "INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, 'usuario')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $passHash);

    if ($stmt->execute()) {
        echo "? Usuario registrado correctamente";
        echo "<br><a href='../public/index.php'>Ir al login</a>";
    } else {
        echo "? Error al registrar: " . $conn->error;
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
<div class="form-container">
    <h2>Registro</h2>
    <form method="post">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrar</button>
    </form>
    <p>¿Ya tienes cuenta? <a href="../public/index.php">Inicia sesión aquí</a></p>
</div>
</body>
</html>
