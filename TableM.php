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

// Obtener el ID del usuario de la sesión
$id_usuario = $_SESSION['id_usuario']; // Asegúrate de que este ID se almacena en la sesión

// Inicializar variable para almacenar datos
$resultado = [];

// Realizar la consulta para obtener datos según el id_usuario
$stmt = $conn->prepare("SELECT * FROM Parametros WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

// Obtener todos los resultados
while ($row = $res->fetch_assoc()) {
    $resultado[] = $row;
}

// Cerrar la declaración
$stmt->close();

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Medidas</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-..." crossorigin="anonymous">
    
    <style>
        .logout-link:hover {
            color: red; /* Cambia el color a rojo al pasar el mouse */
        }
        .active-panel {
            border-radius: 10px;
            padding: 20px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
<div class="container">
    <header class="d-flex justify-content-center py-3">
      <ul class="nav nav-pills">
        <li class="nav-item"><a href="home.php" class="nav-link">Home</a></li>
        <li class="nav-item"><a href="Parametros.php" class="nav-link">Registro de Medidas</a></li>
        <li class="nav-item"><a href="TableM.php" class="nav-link active">Historial de medidas</a></li>
        <li class="nav-item"><a href="Requerimientos.php" class="nav-link">Requerimientos</a></li>
        <li class="nav-item"><a href="Dieta.php" class="nav-link">Dieta</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link logout-link">Cerrar sesión</a></li>
      </ul>
    </header>

    <div class="container mt-4 active-panel">
        <h2>Historial de Medidas</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Peso (kg)</th>
                    <th>Altura (cm)</th>
                    <th>Cintura (cm)</th>
                    <th>Género</th>
                    <th>Edad</th>
                    <th>IMC</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($resultado)): ?>
                    <?php foreach ($resultado as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id_parametro']); ?></td>
                            <td><?php echo htmlspecialchars($row['Peso']); ?></td>
                            <td><?php echo htmlspecialchars($row['Altura']); ?></td>
                            <td><?php echo htmlspecialchars($row['Cintura']); ?></td>
                            <td><?php echo htmlspecialchars($row['Genero']); ?></td>
                            <td><?php echo htmlspecialchars($row['Edad']); ?></td>
                            <td><?php echo htmlspecialchars($row['Imc']); ?></td>
                            <td><?php echo htmlspecialchars($row['Fecha']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No hay registros disponibles.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
