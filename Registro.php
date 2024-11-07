<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Inicializar variables
$correo = $contra = $nombre = $apellido = $telefono = "";
$error = "";
$success = false;

// Procesar el formulario al enviar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $correo = $_POST['correo'];
    $contra = $_POST['contra'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];

    // Prevenir inyecciones SQL
    $correo = $conn->real_escape_string($correo);
    $contra = $conn->real_escape_string($contra);
    $nombre = $conn->real_escape_string($nombre);
    $apellido = $conn->real_escape_string($apellido);
    $telefono = $conn->real_escape_string($telefono);

    // Verificar si el correo ya existe
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM Credenciales WHERE Correo = ?");
    $check_stmt->bind_param("s", $correo);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        $error = "El correo ya está registrado.";
    } else {
        // Insertar en la tabla Credenciales
        $stmt = $conn->prepare("INSERT INTO Credenciales (Correo, Contra) VALUES (?, ?)");
        $stmt->bind_param("ss", $correo, $contra);

        if ($stmt->execute()) {
            // Obtener el ID de la credencial recién insertada
            $id_credencial = $stmt->insert_id;

            // Insertar en la tabla Usuarios
            $stmt_usuario = $conn->prepare("INSERT INTO Usuarios (id_credencial, nombre, apellido, telefono) VALUES (?, ?, ?, ?)");
            $stmt_usuario->bind_param("isss", $id_credencial, $nombre, $apellido, $telefono);

            if ($stmt_usuario->execute()) {
                // Registro exitoso
                $success = true;
            } else {
                $error = "Error al registrar los datos del usuario. Intente nuevamente.";
            }

            // Cerrar declaración de usuario
            $stmt_usuario->close();
        } else {
            $error = "Error al registrar. Intente nuevamente.";
        }

        // Cerrar declaración de credenciales
        $stmt->close();
    }
}

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.1/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative;
        }
        .panel {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            position: relative;
        }
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Asegura que el tamaño incluya padding y border */
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
        .success-alert {
            display: none;
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #dff0d8;
            color: #3c763d;
            padding: 15px;
            font-size: 1.2em;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            width: 40%;
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
    <?php if ($success): ?>
        <div class="success-alert" id="success-alert">Registro exitoso</div>
        <script>
            document.getElementById('success-alert').style.display = 'block';
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 2000); // Redirige después de 2 segundos
        </script>
    <?php endif; ?>

    <div class="panel">
        <h2>Registro</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="Registro.php" method="POST">
        <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej: Edgar" required>
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" placeholder="Ej: Muñoz" required>
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" placeholder="Ej: 123456789" required>
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" placeholder="Ej: ejemplo@gmail.com" required >
            <label for="contra">Contraseña:</label>
            <input type="password" id="contra" name="contra" placeholder="Ej: Cbum08@" required>
           
            <input type="submit" value="Registrarse">
        </form>
        <div class="register-link">
            <span>Ya tienes cuenta? </span><a href="Login.php">Iniciar Sesion</a>
        </div>
    </div>
</body>
</html>
