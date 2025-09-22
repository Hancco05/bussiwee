<?php
session_start();
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    // Caso 1: Admin fijo (no depende de la BD)
    if ($usuario === "admin" && $password === "1234") {
        $_SESSION['usuario_id'] = 0; 
        $_SESSION['usuario_nombre'] = "Administrador";
        $_SESSION['usuario_rol'] = "admin";
        header("Location: ../public/dashboard.php");
        exit;
    }

    // Caso 2: Validar en la BD
    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['usuario_nombre'] = $row['usuario'];
            $_SESSION['usuario_rol'] = $row['rol'];

            header("Location: ../public/dashboard.php");
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
