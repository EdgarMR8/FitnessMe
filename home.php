<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirigir a la página de inicio de sesión si no está logueado
    header('Location: Login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-..." crossorigin="anonymous">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        /* Fondo animado con 3 colores */
        html, body {
            height: 100%;
            margin: 0;
        }

        /* Estilo del fondo animado */
        .bg {
            animation: slide 6s ease-in-out infinite alternate;
            /* Gradiente de 3 colores con más espacio */
            background-image: linear-gradient(-60deg, #6c3 40%, #09f 30%, #6bc 30%);
            bottom: 0;
            left: -50%;
            opacity: 0.5;
            position: fixed;
            right: -50%;
            top: 0;
            z-index: -1;
        }

        /* Variaciones de la animación */
        .bg2 {
            animation-direction: alternate-reverse;
            animation-duration: 4s;
        }

        .bg3 {
            animation-duration: 5s;
        }

        /* Estilo del contenido (texto, botones, etc.) */
        .container {
            position: relative;
            z-index: 1;
        }

        /* Contenedor del contenido (pantalla principal) */
        .content {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 0.25em;
            box-shadow: 0 0 0.25em rgba(0, 0, 0, 0.25);
            box-sizing: border-box;
            left: 50%;
            padding: 10vmin;
            position: fixed;
            text-align: center;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        h1 {
            font-family: monospace;
        }

        /* Animación para mover el fondo */
        @keyframes slide {
            0% {
                transform: translateX(-25%);
            }
            100% {
                transform: translateX(25%);
            }
        }

        /* Estilo para el hover en el enlace de cerrar sesión */
        .logout-link:hover {
            color: red; /* Cambia el color a rojo al pasar el mouse */
        }
    </style>
</head>
<body>
    <!-- Fondo animado -->
    <div class="bg"></div>

    <div class="container">
        <header class="d-flex justify-content-center py-3">
            <ul class="nav nav-pills">
                <li class="nav-item"><a href="home.php" class="nav-link active" aria-current="page">Home</a></li>
                <li class="nav-item"><a href="Parametros.php" class="nav-link">Registro de Medidas</a></li>
                <li class="nav-item"><a href="TableM.php" class="nav-link">Historial de medidas</a></li>
                <li class="nav-item"><a href="Requerimientos.php" class="nav-link">Requerimientos</a></li>
                <li class="nav-item"><a href="Dieta.php" class="nav-link">Dieta</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link logout-link">Cerrar sesión</a></li> <!-- Enlace para cerrar sesión -->
            </ul>
        </header>

        <!-- Contenido principal -->
        <div class="content">
            <h1>Bienvenido a tu nuevo comienzo</h1>
            <p>Lleva el registro de tu proceso.</p>
        </div>
    </div>
</body>
</html>
