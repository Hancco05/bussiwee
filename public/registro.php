<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<form method="post" action="../src/register.php">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contraseña" placeholder="Contraseña" required>
    <button type="submit">Registrar</button>
    <p>¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a></p>
</form>

</body>
</html>
