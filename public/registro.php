<?php
header("Content-Type: text/html; charset=ISO-8859-1");
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $pass = $_POST['contraseña'];
    $rol = $_POST['rol'];

    $passHash = password_hash($pass, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (usuario, contraseña, rol) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $usuario, $passHash, $rol);

    if ($stmt->execute()) {
        echo "? Usuario registrado correctamente.<br><a href='../public/index.php'>Ir al login</a>";
    } else {
        echo "? Error al registrar: " . $conn->error;
    }
}
?>

<form method="post">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contraseña" placeholder="Contrase&ntilde;a" required>
    <select name="rol" required>
        <option value="usuario">Usuario</option>
        <option value="admin">Administrador</option>
    </select>
    <button type="submit">Registrar</button>
</form>
