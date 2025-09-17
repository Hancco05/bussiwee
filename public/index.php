<?php
header("Content-Type: text/html; charset=ISO-8859-1");
?>
<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<form method="post" action="../src/login.php">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contrase&ntilde;a" placeholder="Contrase&ntilde;a" required>
    <button type="submit">Ingresar</button>
    <p>&iquest;No tienes cuenta? <a href="registro.php">Reg&iacute;strate aqu&iacute;</a></p>
</form>
</body>
</html>

