<?php
header('Content-Type: text/html; charset=ISO-8859-1');
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

        // 🔹 DEBUG: muestra qué se recibe
        echo "Contraseña ingresada: " . htmlspecialchars($pass) . "<br>";
        echo "Hash en DB: " . htmlspecialchars($row['contraseña']) . "<br>";

        if (password_verify($pass, $row['contrase�a'])) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['usuario_nombre'] = $usuario;
            $_SESSION['usuario_rol'] = $row['rol']; // ?? Guardar rol en sesi�n
            header("Location: dashboard.php");
            exit;
        } else {
            echo "❌ Contraseña incorrecta";
        }
    } else {
        echo "❌ Usuario no encontrado";
    }
}
?>

<form method="post">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contraseña" placeholder="Contraseña" required>
    <button type="submit">Ingresar</button>
</form>
