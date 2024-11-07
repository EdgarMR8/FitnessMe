<?php
$servername = "192.169.3.103"; // Cambia esto si es necesario
$username = "root";   // Tu usuario de MySQL
$password = "Edgar121413"; // Tu contraseña de MySQL
$dbname = "Fitness";         // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

