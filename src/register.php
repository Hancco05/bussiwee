<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $pass = $_POST['contrase�a'];
    $rol = $_POST['rol']; // ?? Nuevo

    // Generar hash
    $passHash = password_hash($pass, PASSWORD_DEFAULT);

    // Insertar usuario en la BD
    $sql = "INSERT INTO usuarios (usuario, contrase�a, rol) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $usuario, $passHash, $rol);

    if ($stmt->execute()) {
        echo "? Usuario registrado correctamente";
        echo "<br><a href='../public/index.php'>Ir al login</a>";
    } else {
        echo "? Error al registrar: " . $conn->error;
    }
}
?>

<form method="post">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contrase�a" placeholder="Contrase�a" required>
    <select name="rol" required>
        <option value="usuario">Usuario</option>
        <option value="admin">Administrador</option>
    </select>
    <button type="submit">Registrar</button>
</form>
