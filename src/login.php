<?php
session_start();
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $pass = $_POST['contraseña'];

    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['contraseña'])) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['usuario_nombre'] = $usuario;
            $_SESSION['usuario_rol'] = $row['rol'];
            header("Location: dashboard.php");
            exit;
        } else {
            echo "? Contrase&ntilde;a incorrecta";
        }
    } else {
        echo "? Usuario no encontrado";
    }
}
?>


<form method="post">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contraseÃ±a" placeholder="ContraseÃ±a" required>
    <button type="submit">Ingresar</button>
</form>
