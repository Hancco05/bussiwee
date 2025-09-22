<?php
session_start();
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    // Caso especial: admin hardcodeado
    if ($usuario === "admin" && $password === "1234") {
        $_SESSION['usuario_id'] = 0;
        $_SESSION['usuario_nombre'] = "Administrador";
        $_SESSION['usuario_rol'] = "admin";
        header("Location: admin_panel.php");
        exit;
    }

    // Usuario normal desde BD phpMyAdmin
    $stmt = $conn->prepare("SELECT id, usuario, password, rol FROM usuarios WHERE usuario=?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['usuario_nombre'] = $row['usuario'];
            $_SESSION['usuario_rol'] = $row['rol'];

            header("Location: ../src/dashboard.php");
            exit;
        } else {
            header("Location: ../public/index.php?error=Contraseña incorrecta");
            exit;
        }
    } else {
        header("Location: ../public/index.php?error=Usuario no encontrado");
        exit;
    }
}
?>
