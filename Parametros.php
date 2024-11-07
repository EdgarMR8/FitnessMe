<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: Login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parametros</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-..." crossorigin="anonymous">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body{
            background: #cdf1e7;
        }
        .logout-link:hover {
            color: red; /* Cambia el color a rojo al pasar el mouse */
        }
        .active-panel {
            border-radius: 10px;
            padding: 20px;
            background-color: #f9f9f9;
            
        }
        .form-container {
            max-width: 500px; /* Ancho máximo del contenedor */
            margin: auto; /* Centra el contenedor */
            position: relative;
            z-index: 2; /* Aseguramos que el contenido esté sobre las frutas */
        }
        h2 {
            text-align: center; /* Centra el texto del encabezado */
        }
        /* Animación de la lluvia  */
        .gym {
            position: absolute;
            top: -50px;
            animation: fall 5s linear infinite;
        }
         /* Animación de caída  */
         @keyframes fall {
            to {
                transform: translateY(100vh) rotate(360deg); /* caen hasta el fondo */
            }
        }
    </style>
    <script>
        function calcularIMC() {
            var peso = parseFloat(document.getElementById('peso').value);
            var altura = parseFloat(document.getElementById('altura').value);
            var imc = 0;

            if (!isNaN(peso) && !isNaN(altura) && altura > 0) {
                imc = (peso / ((altura / 100) * (altura / 100))).toFixed(2); // Cálculo de IMC
            }

            document.getElementById('imc').value = imc; // Mostrar IMC en el campo
        }
    </script>
</head>
<body>
<div class="container">
    <header class="d-flex justify-content-center py-3">
      <ul class="nav nav-pills">
        <li class="nav-item"><a href="home.php" class="nav-link">Home</a></li>
        <li class="nav-item"><a href="Parametros.php" class="nav-link active" aria-current="page">Registro de Medidas</a></li>
        <li class="nav-item"><a href="TableM.php" class="nav-link">Historial de medidas</a></li>
        <li class="nav-item"><a href="Requerimientos.php" class="nav-link">Requerimientos</a></li>
        <li class="nav-item"><a href="Dieta.php" class="nav-link">Dieta</a></li>
        <li class="nav-item"><a href="logout.php" class="nav-link logout-link">Cerrar sesión</a></li>
      </ul>
    </header>

    <div class="container mt-4 form-container"> <!-- Clase añadida aquí -->
        <h2>Registro de Medidas</h2>
        <div class="active-panel">
            <form action="Post_Medidas.php" method="POST">
                <div class="mb-3">
                    <label for="peso" class="form-label">Peso (kg)</label>
                    <input type="number" step="0.01" class="form-control" id="peso" name="peso" oninput="calcularIMC()" placeholder="Ej:115" required>
                </div>
                <div class="mb-3">
                    <label for="altura" class="form-label">Altura (cm)</label>
                    <input type="number" step="0.01" class="form-control" id="altura" name="altura" oninput="calcularIMC()" placeholder="Ej:176" required>
                </div>
                <div class="mb-3">
                    <label for="cintura" class="form-label">Cintura (cm) <small>(opcional)</small></label>
                    <input type="number" step="0.01" class="form-control" id="cintura" name="cintura" placeholder="Ej:88">
                </div>
                <div class="mb-3">
                    <label for="genero" class="form-label">Género</label>
                    <select class="form-select" id="genero" name="genero" required>
                        <option value="">Seleccione...</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="edad" class="form-label">Edad</label>
                    <input type="number" class="form-control" id="edad" name="edad" placeholder="Ej:24" required>
                </div>
                <div class="mb-3">
                    <label for="imc" class="form-label">IMC</label>
                    <input type="text" class="form-control" id="imc" name="imc" placeholder="Generado automáticamente" readonly>
                </div>
                <div class="text-center mb-3"> <!-- Contenedor para centrar el botón -->
                    <button type="submit" name="submit_parametros" class="btn btn-secondary">Registrar Medidas</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Animación de lluvia 
    setInterval(function() {
        let gyms = [
            'iconos/p1.png',   
            'iconos/p2.png',   
            'iconos/p3.png',   
            'iconos/p4.png',     
            'iconos/p5.png'      
        ];

        // Selección aleatoria de iconos 
        let gym = gyms[Math.floor(Math.random() * gyms.length)];
        let leftPosition = Math.random() * 100;

        let gymElement = document.createElement('div');
        gymElement.classList.add('gym');
        gymElement.style.left = leftPosition + '%';
        gymElement.innerHTML = `<img src="${gym}" alt="Fruta" width="40">`;  // Usando el ícono 
        document.body.appendChild(gymElement);

        // Eliminar cuando haya caído completamente
        setTimeout(() => {
            gymElement.remove();
        }, 5000); // Tiempo de duración de la animación (5 segundos)
    }, 1000); // Crear una nueva cada 1 segundo
</script>
</body>
</html>
