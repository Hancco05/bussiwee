<?php
session_start();

// si no hay sesión activa, redirige al login
if(!isset($_SESSION['usuario_id'])){
    header("Location: login.php");
    exit;
}

echo "<h1>Bienvenido, " . htmlspecialchars($_SESSION['usuario_nombre']) . " 👋</h1>";
echo "<p>Has ingresado correctamente al dashboard.</p>";
echo '<a href="logout.php">Cerrar sesión</a>';
?>
