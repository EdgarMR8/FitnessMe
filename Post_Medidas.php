<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.php');
    exit;
}

// Conectar a la base de datos
include 'conexion.php';

// Inicializar variables para el formulario
$error = "";
$success = "";

// Procesar el formulario de inserción de medidas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_parametros'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $peso = $_POST['peso'];
    $altura = $_POST['altura'];
    $cintura = !empty($_POST['cintura']) ? $_POST['cintura'] : NULL; // Asignar NULL si está vacío
    $genero = $_POST['genero'];
    $edad = $_POST['edad'];
    $fecha = date('Y-m-d'); // Fecha actual
    $imc = $_POST['imc'];

    // Validar y sanitizar entradas
    if (is_numeric($peso) && is_numeric($altura) && is_numeric($edad) && ($cintura === NULL || is_numeric($cintura))) {
        // Prevenir inyecciones SQL
        $peso = $conn->real_escape_string($peso);
        $altura = $conn->real_escape_string($altura);
        $cintura = $cintura !== NULL ? $conn->real_escape_string($cintura) : NULL; // Manejo de NULL
        $genero = $conn->real_escape_string($genero);
        $edad = $conn->real_escape_string($edad);
        $imc = $conn->real_escape_string($imc);

        // Insertar datos en la tabla Parametros
        $stmt = $conn->prepare("INSERT INTO Parametros (id_usuario, Peso, Altura, Cintura, Genero, Imc, Edad, Fecha) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        // Ajustar el tipo de la declaración
        if ($cintura === NULL) {
            $stmt->bind_param("iiddssss", $id_usuario, $peso, $altura, $cintura, $genero, $imc, $edad, $fecha);
        } else {
            $stmt->bind_param("iiddssss", $id_usuario, $peso, $altura, $cintura, $genero, $imc, $edad, $fecha);
        }

        if ($stmt->execute()) {
            $success = "Medidas registradas exitosamente.";
            header("Location: TableM.php");
            exit;
        } else {
            $error = "Error al registrar las medidas: " . $stmt->error; // Cambiado para mostrar el error de la declaración
        }

        // Cerrar declaración
        $stmt->close();
    } else {
        $error = "Por favor, asegúrate de que todos los datos son válidos.";
    }
}

// Cerrar conexión
$conn->close();

