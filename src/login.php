<?php
session_start();
require_once("../config/db.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $usuario = $_POST['usuario'];
    $pass = $_POST['contraseña'];

    $stmt = $conn->prepare("SELECT id, contraseña FROM usuarios WHERE usuario=?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->bind_result($id, $hash);

    if($stmt->fetch()){
        if(password_verify($pass, $hash)){
            $_SESSION['usuario_id'] = $id;
            $_SESSION['usuario_nombre'] = $usuario;
            header("Location: dashboard.php");
            exit;
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "Usuario no encontrado";
    }
    $stmt->close();
}
?>

<form method="post">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contraseña" placeholder="Contraseña" required>
    <button type="submit">Ingresar</button>
</form>
