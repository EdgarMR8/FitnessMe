<?php
// Incluir el archivo de conexión
include 'conexion.php';
session_start(); // Iniciar la sesión

// Inicializar variables
$correo = $contra = "";
$error = "";

// Procesar el formulario al enviar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $correo = $_POST['correo'];
    $contra = $_POST['contra'];

    // Prevenir inyecciones SQL
    $correo = $conn->real_escape_string($correo);
    $contra = $conn->real_escape_string($contra);

    // Consultar la base de datos
    $stmt = $conn->prepare("
        SELECT U.id_usuario 
        FROM Credenciales C 
        JOIN Usuarios U ON C.Id_C = U.id_credencial 
        WHERE C.Correo = ? AND C.Contra = ?
    ");
    $stmt->bind_param("ss", $correo, $contra);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Credenciales correctas, obtener el id_usuario
        $row = $result->fetch_assoc();
        $_SESSION['loggedin'] = true;
        $_SESSION['id_usuario'] = $row['id_usuario']; // Almacenar id_usuario en la sesión
        header("Location: home.php");
        exit();
    } else {
        // Credenciales incorrectas
        $error = "Correo o contraseña incorrectos.";
    }

    // Cerrar declaración
    $stmt->close();
}

// Cerrar conexión
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        /* Fondo de cielo con nubes en movimiento */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(to top, #87CEEB, #00BFFF); /* Cielo azul */
            overflow: hidden;
            position: relative;
        }

        /* Nubes flotando de fondo */
        .clouds {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .clouds img {
            position: absolute;
            animation: moveClouds 30s linear infinite;
        }

        .clouds img:nth-child(1) {
            width: 250px;
            top: 20%;
            left: -250px;
            animation-duration: 40s;
        }

        .clouds img:nth-child(2) {
            width: 300px;
            top: 50%;
            left: -300px;
            animation-duration: 50s;
        }

        .clouds img:nth-child(3) {
            width: 200px;
            top: 80%;
            left: -200px;
            animation-duration: 60s;
        }

        @keyframes moveClouds {
            100% {
                transform: translateX(100vw);
            }
        }

        /* Estilos del panel de login */
        .panel {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            z-index: 1;
            position: relative;
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="email"],
        input[type="password"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #4cae4c;
        }

        .error {
            color: red;
            text-align: center;
        }

        .register-link {
            display: block;
            text-align: center;
            margin-top: 15px;
        }

        .register-link a {
            text-decoration: none;
            color: #007bff;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="clouds">
        <!-- Imágenes de nubes -->
        <img src="nube.png" alt="Nube 1">
        <img src="nube.png" alt="Nube 2">
        <img src="nube.png" alt="Nube 3">
    </div>

    <div class="panel">
        <h2>Iniciar Sesión</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" placeholder="Ingresar Correo" required>
            <label for="contra">Contraseña:</label>
            <input type="password" id="contra" name="contra" placeholder="Ingresar Contraseña" required>
            <input type="submit" value="Acceder">
        </form>
        <div class="register-link">
            <span>Si no tienes cuenta, </span><a href="Registro.php">regístrate aquí</a>
        </div>
    </div>
</body>
</html>
