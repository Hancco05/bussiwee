<?php
header("Content-Type: text/html; charset=ISO-8859-1");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Registro</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<form method="post" action="../src/register.php">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contraseña" placeholder="Contraseña" required>
    <button type="submit">Registrar</button>
    <p>¿Ya tienes cuenta? <a href="index.php">Inicia sesi&oacute;n aqu&iacute;</a></p>
</form>

</body>
</html>
