<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $usuario = $_POST['usuario'];
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT); // encripta la contraseña

    $conn = new mysqli('localhost', 'root', '', 'mi_proyecto');
    if($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, contraseña) VALUES (?, ?)");
    $stmt->bind_param("ss", $usuario, $contraseña);
    if($stmt->execute()){
        echo "Usuario registrado!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>

<form method="post">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contraseña" placeholder="Contraseña" required>
    <button type="submit">Registrar</button>
</form>
