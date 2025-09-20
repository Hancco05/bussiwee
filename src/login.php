<?php
session_start();
require_once "../config/config.php";

// Si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    // Caso especial: admin/1234
    if ($usuario === "admin" && $password === "1234") {
        $_SESSION['usuario_id'] = 0; 
        $_SESSION['usuario_nombre'] = "Administrador";
        $_SESSION['usuario_rol'] = "admin";
        header("Location: ../src/dashboard.php");
        exit;
    }

    // Caso: usuario desde la base de datos
    $sql = "SELECT id, usuario, password, rol FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verificamos la contraseña encriptada
        if (password_verify($password, $row['password'])) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['usuario_nombre'] = $row['usuario'];
            $_SESSION['usuario_rol'] = $row['rol'];
            header("Location: ../src/dashboard.php");
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>




<form method="post">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contraseÃ±a" placeholder="ContraseÃ±a" required>
    <button type="submit">Ingresar</button>
</form>
