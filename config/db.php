<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "mi_proyecto";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Error de conexi�n: " . $conn->connect_error);
}
?>
