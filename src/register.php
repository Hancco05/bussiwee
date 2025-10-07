<?php
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    if (!empty($usuario) && !empty($password)) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Verificar si existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario=?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Crear usuario normal (rol = usuario)
            $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, 'usuario')");
            $stmt->bind_param("ss", $usuario, $passwordHash);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            header("Location: ../public/index.php?registro=ok");
            exit;
        } else {
            header("Location: ../public/index.php?error=Usuario ya existe");
            exit;
        }
    } else {
        header("Location: ../public/index.php?error=Completa todos los campos");
        exit;
    }
}
?>


<form method="post">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contraseña" placeholder="Contraseña" required>
    <select name="rol" required>
        <option value="usuario">Usuario</option>
        <option value="admin">Administrador</option>
    </select>
    <button type="submit">Registrar</button>
</form>
