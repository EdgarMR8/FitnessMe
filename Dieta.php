<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.php');
    exit;
}

// Incluir archivo de conexión
include 'Conexion.php';

// Inicializar mensajes de sesión
if (!isset($_SESSION['success_insert_message'])) {
    $_SESSION['success_insert_message'] = '';
}
if (!isset($_SESSION['success_delete_message'])) {
    $_SESSION['success_delete_message'] = '';
}
if (!isset($_SESSION['error_message'])) {
    $_SESSION['error_message'] = '';
}

// Manejar la inserción de datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['id_usuario']; // Suponiendo que el id_usuario se almacena en la sesión
    $cantidad = $_POST['cantidad'];
    $producto = $_POST['producto'];
    $calorias = $_POST['calorias'];
    $proteina = $_POST['proteina'];
    $tabla = $_POST['tab'];  // Obtener la tabla seleccionada

    // Verificar que la tabla seleccionada es válida
    $valid_tables = ['Desayuno', 'Almuerzo', 'Cena'];
    if (in_array($tabla, $valid_tables)) {
        // Crear la consulta dinámica
        $sql = "INSERT INTO $tabla (id_usuario, cantidad, producto, calorias, proteina) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issdi", $id_usuario, $cantidad, $producto, $calorias, $proteina);

        if ($stmt->execute()) {
            $_SESSION['success_insert_message'] = "<div id='success-insert-message' class='alert alert-success'>Producto añadido exitosamente.</div>";
        } else {
            $_SESSION['error_message'] = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
    } else {
        $_SESSION['error_message'] = "<div class='alert alert-danger'>Error: Tabla no válida seleccionada.</div>";
    }
}

// Manejar la eliminación de datos
if (isset($_GET['delete']) && isset($_GET['table'])) {
    // Obtener los valores de id y tabla
    $id = $_GET['delete'];
    $table = $_GET['table'];

    // Validar que la tabla es válida
    $valid_tables = ['Desayuno', 'Almuerzo', 'Cena'];
    if (in_array($table, $valid_tables)) {
        // Establecer el nombre de la columna de id según la tabla
        switch ($table) {
            case 'Desayuno':
                $column_id = 'id_Des';
                break;
            case 'Almuerzo':
                $column_id = 'id_Alm';
                break;
            case 'Cena':
                $column_id = 'id_Cena';
                break;
            default:
                // Si no coincide con ninguna tabla, salir con un error
                $_SESSION['error_message'] = "<div class='alert alert-danger'>Error: Tabla no válida seleccionada.</div>";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
        }

        // Crear la consulta de eliminación
        $sql = "DELETE FROM $table WHERE $column_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        // Ejecutar la eliminación
        if ($stmt->execute()) {
            $_SESSION['success_delete_message'] = "<div id='success-delete-message' class='alert alert-success'>$table eliminado exitosamente.</div>";
        } else {
            $_SESSION['error_message'] = "<div class='alert alert-danger'>Error al eliminar: " . $stmt->error . "</div>";
        }

        // Redirigir para evitar reenvíos de formulario
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $_SESSION['error_message'] = "<div class='alert alert-danger'>Error: Tabla no válida seleccionada.</div>";
    }
}


// Asegúrate de que la variable $sql_desayuno esté definida antes de usarla.
$sql_desayuno = "SELECT id_Des, cantidad, producto, calorias, proteina FROM Desayuno WHERE id_usuario = ?";
$sql_almuerzo = "SELECT id_Alm, cantidad, producto, calorias, proteina FROM Almuerzo WHERE id_usuario = ?";
$sql_cena = "SELECT id_Cena, cantidad, producto, calorias, proteina FROM Cena WHERE id_usuario = ?";

// Preparar y ejecutar la consulta para los desayunos
$stmt = $conn->prepare($sql_desayuno);
$stmt->bind_param("i", $_SESSION['id_usuario']);
$stmt->execute();
$result_desayuno = $stmt->get_result();

// Preparar y ejecutar la consulta para los almuerzos
$stmt = $conn->prepare($sql_almuerzo);
$stmt->bind_param("i", $_SESSION['id_usuario']);
$stmt->execute();
$result_almuerzo = $stmt->get_result();

// Preparar y ejecutar la consulta para las cenas
$stmt = $conn->prepare($sql_cena);
$stmt->bind_param("i", $_SESSION['id_usuario']);
$stmt->execute();
$result_cena = $stmt->get_result();


// Inicializar totales
$total_calorias = 0;
$total_proteina = 0;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dieta</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-..." crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> <!-- Carga Bootstrap Icons -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body{
            background: #87ceeb;
        }
        .logout-link:hover {
            color: red;
        }
        .separator {
            margin: 20px 0;
            border-top: 1px solid #ccc;
            color: black;
        }

        /* Posicionamiento de las tablas por encima de las frutas */
        .container {
            position: relative;
            z-index: 2; /* Aseguramos que el contenido esté sobre las frutas */
        }

        /* Animación de la lluvia de frutas */
        .fruit {
            position: absolute;
            top: -50px;
            animation: fall 5s linear infinite;
        }

        /* Animación de caída de frutas */
        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(360deg); /* Las frutas caen hasta el fondo */
            }
        }
         /* Estilo para las tablas */
    table {
        border-collapse: separate;  /* Esto asegura que el border-radius funcione */
        border-spacing: 0;  /* Elimina cualquier espacio entre celdas */
        width: 100%;
        border: 2px solid #ddd;  /* Agrega un borde a la tabla */
        border-radius: 10px;  /* Bordes redondeados en toda la tabla */
        overflow: hidden;  /* Asegura que el borde redondeado se vea correctamente */
    }

label{
font-weight: bold;
}
   
    </style>
</head>
<body>
<div class="container">
    <header class="d-flex justify-content-center py-3">
      <ul class="nav nav-pills">
        <li class="nav-item"><a href="home.php" class="nav-link" aria-current="page">Home</a></li>
        <li class="nav-item"><a href="Parametros.php" class="nav-link">Registro de Medidas</a></li>
        <li class="nav-item"><a href="TableM.php" class="nav-link">Historial de medidas</a></li>
        <li class="nav-item"><a href="Requerimientos.php" class="nav-link">Requerimientos</a></li>
        <li class="nav-item"><a href="Dieta.php" class="nav-link active">Dieta</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link logout-link">Cerrar sesión</a></li>
      </ul>
    </header>

    <div class="mt-4">
        <h2>Agregar Productos</h2>
        <div class="separator"></div>
        <form method="POST" action="">

            <div class="mb-3">
                <label for="tab" class="form-label">Selecciona una comida</label>
                <select name="tab" id="tab" class="form-select" required>
                    <option value="" disabled selected>Selecciona una comida</option>
                    <option value="Desayuno">Desayuno</option>
                    <option value="Almuerzo">Almuerzo</option>
                    <option value="Cena">Cena</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="cantidad" class="form-label">Cantidad</label>
                <input type="text" class="form-control" id="cantidad" name="cantidad" placeholder="ej: 1 / 150g / 200 ml" required>
            </div>
            <div class="mb-3">
                <label for="producto" class="form-label">Producto</label>
                <input type="text" class="form-control" id="producto" name="producto" placeholder="ej: Molida de res" required>
            </div>
            <div class="mb-3">
                <label for="calorias" class="form-label">Calorías</label>
                <input type="number" class="form-control" id="calorias" name="calorias" placeholder="ej: 400" required>
            </div>
            <div class="mb-3">
                <label for="proteina" class="form-label">Proteína (g)</label>
                <input type="number" step="0.01" class="form-control" id="proteina" name="proteina" placeholder="ej: 50" required>
            </div>
            <button type="submit" class="btn btn-primary">Añadir Producto</button>
        </form>
    </div>

    <div class="mt-4">
        <?php
        // Mostrar mensaje de éxito o error
        if (!empty($_SESSION['success_insert_message'])) {
            echo $_SESSION['success_insert_message'];
            $_SESSION['success_insert_message'] = ''; // Limpiar mensaje después de mostrar
        }
        if (!empty($_SESSION['success_delete_message'])) {
            echo $_SESSION['success_delete_message'];
            $_SESSION['success_delete_message'] = ''; // Limpiar mensaje después de mostrar
        }
        if (!empty($_SESSION['error_message'])) {
            echo $_SESSION['error_message'];
            $_SESSION['error_message'] = ''; // Limpiar mensaje después de mostrar
        }
        ?>
        
        <h2>Desayuno</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Cantidad</th>
                    <th>Producto</th>
                    <th>Calorías</th>
                    <th>Proteína (g)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_desayuno->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['cantidad']); ?></td>
                        <td><?php echo htmlspecialchars($row['producto']); ?></td>
                        <td><?php echo htmlspecialchars($row['calorias']); ?></td>
                        <td><?php echo htmlspecialchars($row['proteina']); ?></td>
                        <td>
                        <a href="?delete=<?php echo $row['id_Des']; ?>&table=Desayuno" class="text-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este desayuno?');">
                        <i class="bi bi-trash3"></i>
                        </a>
                        </td>
                    </tr>
                    <?php
                    // Sumar totales
                    $total_calorias += $row['calorias'];
                    $total_proteina += $row['proteina'];
                    ?>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="separator"></div>
        <h2>Almuerzo</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Cantidad</th>
                    <th>Producto</th>
                    <th>Calorías</th>
                    <th>Proteína (g)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_almuerzo->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['cantidad']); ?></td>
                        <td><?php echo htmlspecialchars($row['producto']); ?></td>
                        <td><?php echo htmlspecialchars($row['calorias']); ?></td>
                        <td><?php echo htmlspecialchars($row['proteina']); ?></td>
                        <td>
                        <a href="?delete=<?php echo $row['id_Alm']; ?>&table=Almuerzo" class="text-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este Almuerzo?');">
                        <i class="bi bi-trash3"></i>
                        </a>
                        </td>
                    </tr>
                    <?php
                    // Sumar totales
                    $total_calorias += $row['calorias'];
                    $total_proteina += $row['proteina'];
                    ?>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="separator"></div>
        <h2>Cena</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Cantidad</th>
                    <th>Producto</th>
                    <th>Calorías</th>
                    <th>Proteína (g)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_cena->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['cantidad']); ?></td>
                        <td><?php echo htmlspecialchars($row['producto']); ?></td>
                        <td><?php echo htmlspecialchars($row['calorias']); ?></td>
                        <td><?php echo htmlspecialchars($row['proteina']); ?></td>
                        <td>
                        <a href="?delete=<?php echo $row['id_Cena']; ?>&table=Cena" class="text-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar esta Cena?');">
                        <i class="bi bi-trash3"></i>
                        </a>
                        </td>
                    </tr>
                    <?php
                    // Sumar totales
                    $total_calorias += $row['calorias'];
                    $total_proteina += $row['proteina'];
                    ?>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="separator"></div>
        <div class="mt-3">
            <h5>Total Calorías: <?php echo $total_calorias; ?> Kcal</h5>
            <h5>Total Proteína: <?php echo $total_proteina; ?> g</h5>
        </div>
    </div>
</div>

<script>
    // Animación de lluvia de frutas
    setInterval(function() {
        let fruits = [
            'iconos/manzana.png',   // Manzana
            'iconos/platanos.png',   // Plátano
            'iconos/naranja.png',   // Naranja
            'iconos/fresa.png',     // Fresa
            'iconos/uva.png'        // Uva
        ];

        // Selección aleatoria de frutas
        let fruit = fruits[Math.floor(Math.random() * fruits.length)];
        let leftPosition = Math.random() * 100;

        let fruitElement = document.createElement('div');
        fruitElement.classList.add('fruit');
        fruitElement.style.left = leftPosition + '%';
        fruitElement.innerHTML = `<img src="${fruit}" alt="Fruta" width="40">`;  // Usando el ícono de la fruta
        document.body.appendChild(fruitElement);

        // Eliminar la fruta cuando haya caído completamente
        setTimeout(() => {
            fruitElement.remove();
        }, 5000); // Tiempo de duración de la animación (5 segundos)
    }, 1000); // Crear una fruta nueva cada 1 segundo
</script>

</body>
</html>

