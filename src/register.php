<?php
require_once("../config/db.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $usuario = $_POST['usuario'];
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, contraseña) VALUES (?, ?)");
    $stmt->bind_param("ss", $usuario, $contraseña);

    if($stmt->execute()){
        echo "Usuario registrado correctamente <a href='login.php'>Iniciar sesión</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<form method="post">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contraseña" placeholder="Contraseña" required>
    <button type="submit">Registrar</button>
</form>
