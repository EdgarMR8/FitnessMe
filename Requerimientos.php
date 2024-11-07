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

// Obtener el último registro de parámetros del usuario
$stmt = $conn->prepare("SELECT id_parametro, Peso, Altura, Genero, Edad FROM Parametros WHERE id_usuario = ? ORDER BY id_parametro DESC LIMIT 1");
$stmt->bind_param("i", $_SESSION['id_usuario']);
$stmt->execute();
$result = $stmt->get_result();
$registro = $result->fetch_assoc();
$stmt->close();

// Calcular TMB usando la nueva fórmula
if ($registro) {
    $peso = $registro['Peso'];
    $altura = $registro['Altura'];
    $edad = $registro['Edad'];
    $genero = $registro['Genero'];

    // Calcular TMB según el género con las nuevas fórmulas
    if ($genero == 'Masculino') {
        $tmb = (10 * $peso) + (6.25 * $altura) - (5 * $edad) + 5;
    } else {
        $tmb = (10 * $peso) + (6.25 * $altura) - (5 * $edad) - 161;
    }
    
    // Calcular la ingesta de proteína
    $proteinaNecesaria = $peso * 1.6; // 1.6 gramos de proteína por kg de peso
} else {
    $tmb = 0;
    $proteinaNecesaria = 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requerimientos</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-..." crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .logout-link:hover {
            color: red; /* Cambia el color a rojo al pasar el mouse */
        }
        .panel-pequeno {
            max-width: 600px; /* Ajusta el ancho máximo */
            margin: auto; /* Centrar el panel */
        }
        h2, h3 {
            text-align: center; /* Centra el texto del encabezado */
        }
        .form-section, .table-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%; /* Igualar la altura */
        }
    </style>
    <script>
        $(document).ready(function() {
            $('#nivel_actividad').change(function() {
                var nivelActividad = $(this).val(); // Captura el valor seleccionado
                $('#resultado').text('Nivel de Actividad Física: ' + nivelActividad); // Muestra el valor en el label
                
                // Calcular y mostrar el total de calorías necesarias
                var tmb = <?php echo $tmb; ?>; // Obtener el TMB desde PHP
                var caloriasNecesarias = Math.round(tmb * nivelActividad); // Calcular y redondear las calorías necesarias
                $('#calorias_necesarias').val(caloriasNecesarias); // Mostrar el resultado

                // Calcular y mostrar el déficit y bulk
                var deficit = caloriasNecesarias - 500;
                var bulk = caloriasNecesarias + 500;
                $('#deficit').val(deficit); // Mostrar el déficit
                $('#bulk').val(bulk); // Mostrar el bulk
            });
        });
    </script>
</head>
<body>
<div class="container">
    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="home.php" class="nav-link">Home</a></li>
            <li class="nav-item"><a href="Parametros.php" class="nav-link">Registro de Medidas</a></li>
            <li class="nav-item"><a href="TableM.php" class="nav-link">Historial de medidas</a></li>
            <li class="nav-item"><a href="Requerimientos.php" class="nav-link active">Requerimientos</a></li>
            <li class="nav-item"><a href="Dieta.php" class="nav-link">Dieta</a></li>
            <li class="nav-item"><a href="logout.php" class="nav-link logout-link">Cerrar sesión</a></li>
        </ul>
    </header>

    <div class="mt-4">
        <div class="row justify-content-center align-items-start"> <!-- Nueva fila para el grid -->
            <div class="col-md-6"> <!-- Columna para el formulario -->
                <div class="card panel-pequeno"> 
                    <div class="card-body">
                        <h2>Datos</h2> <!-- Mover el título dentro del panel -->
                        <form>
                            <div class="mb-3">
                                <label for="peso" class="form-label">Peso (kg)</label>
                                <input type="text" class="form-control" id="peso" value="<?php echo $registro['Peso']; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="altura" class="form-label">Altura (cm)</label>
                                <input type="text" class="form-control" id="altura" value="<?php echo $registro['Altura']; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="genero" class="form-label">Género</label>
                                <input type="text" class="form-control" id="genero" value="<?php echo $registro['Genero']; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="edad" class="form-label">Edad</label>
                                <input type="text" class="form-control" id="edad" value="<?php echo $registro['Edad']; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="nivel_actividad" class="form-label">Nivel de Actividad Física</label>
                                <select class="form-select" id="nivel_actividad">
                                    <option value="" disabled selected>Selecciona una opción</option>
                                    <option value="1.2">Sedentario: poco o nada de ejercicio al día</option>
                                    <option value="1.375">Actividad Ligera: ejercicio ligero 1-3 días</option>
                                    <option value="1.55">Actividad Moderada: ejercicio moderado de 3-5 días</option>
                                    <option value="1.725">Actividad Intensa: ejercicio intenso de 6-7 días</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6"> <!-- Columna para la tabla -->
                <div class="card panel-pequeno"> 
                    <div class="card-body">
                        <h3>Requerimientos</h3>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Gasto Energético Diario (cal)</td>
                                    <td><input type="text" class="form-control" id="calorias_necesarias" value="0" readonly></td>
                                </tr>
                                <tr>
                                    <td>Ingesta de Proteína Recomendada (g)</td>
                                    <td><input type="text" class="form-control" id="proteina_necesaria" value="<?php echo round($proteinaNecesaria); ?>" readonly></td>
                                </tr>
                                <tr>
                                    <td>Déficit Calórico (cal)</td>
                                    <td><input type="text" class="form-control" id="deficit" value="0" readonly></td>
                                </tr>
                                <tr>
                                    <td>Bulk Calórico (cal)</td>
                                    <td><input type="text" class="form-control" id="bulk" value="0" readonly></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
