<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>
<h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?> 👋</h1>
<p>Has ingresado correctamente al dashboard.</p>
<a href="logout.php">Cerrar sesión</a>
