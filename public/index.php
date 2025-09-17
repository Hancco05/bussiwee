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
    <input type="password" name="contraseña" placeholder="Contraseña" required>
    <button type="submit">Ingresar</button>
    <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
</form>
</body>
</html>

