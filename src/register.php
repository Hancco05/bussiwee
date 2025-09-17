<?php
header('Content-Type: text/html; charset=ISO-8859-1');
session_start();
include '../config/db.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $pass = $_POST['contraseña'];

    // Generar hash
    $passHash = password_hash($pass, PASSWORD_DEFAULT);

    // Insertar usuario en la BD
    $sql = "INSERT INTO usuarios (usuario, contraseña) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $passHash);

    if ($stmt->execute()) {
        echo "✅ Usuario registrado correctamente";
        echo "<br><a href='../public/index.php'>Ir al login</a>";
    } else {
        echo "❌ Error al registrar: " . $conn->error;
    }
}
?>



<form method="post">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contraseña" placeholder="Contraseña" required>
    <button type="submit">Registrar</button>
</form>
