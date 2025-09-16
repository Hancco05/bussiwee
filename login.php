<?php
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $usuario = $_POST['usuario'];
    $pass = $_POST['contraseña'];

    $conn = new mysqli('localhost', 'root', '', 'mi_proyecto');
    if($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

    $stmt = $conn->prepare("SELECT id, contraseña FROM usuarios WHERE usuario=?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->bind_result($id, $hash);
    if($stmt->fetch()){
        if(password_verify($pass, $hash)){
            $_SESSION['usuario_id'] = $id;
            $_SESSION['usuario_nombre'] = $usuario;
            echo "Login exitoso!";
            // Aquí puedes redirigir al dashboard
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "Usuario no encontrado";
    }
    $stmt->close();
    $conn->close();
}
if(password_verify($pass, $hash)){
    $_SESSION['usuario_id'] = $id;
    $_SESSION['usuario_nombre'] = $usuario;
    header("Location: dashboard.php"); // redirige al panel
    exit;
} else {
    echo "Contraseña incorrecta";
}

?>

<form method="post">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contraseña" placeholder="Contraseña" required>
    <button type="submit">Ingresar</button>
</form>
