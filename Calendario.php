<?php
// calendario.php
session_start();

// Conectar a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "roles");

if (!$conexion) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Autenticación del usuario (si es necesario para la funcionalidad)
if (isset($_POST['usuario']) && isset($_POST['contraseña'])) {
    $usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $contraseña = mysqli_real_escape_string($conexion, $_POST['contraseña']);

    $consulta = "SELECT * FROM usuarios WHERE usuario='$usuario' AND contraseña='$contraseña'";
    $resultado = mysqli_query($conexion, $consulta);

    if (mysqli_num_rows($resultado) > 0) {
        // Usuario autenticado
        $_SESSION['usuario'] = $usuario;
    } else {
        // Autenticación fallida
        die("Usuario o contraseña incorrectos.");
    }
}

// Verificar si se ha enviado una solicitud AJAX para comprobar la disponibilidad de la fecha
if (isset($_POST['action']) && $_POST['action'] == 'check_date') {
    $fecha_seleccionada = mysqli_real_escape_string($conexion, $_POST['fecha']);
    $stmt = $conexion->prepare("SELECT * FROM hacienda WHERE fecha = ?");
    $stmt->bind_param("s", $fecha_seleccionada);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'unavailable']);
    } else {
        echo json_encode(['status' => 'available']);
    }
    $stmt->close();
    exit;
}

// Verificar si se ha enviado una solicitud AJAX para guardar la fecha
if (isset($_POST['action']) && $_POST['action'] == 'save_date') {
    $fecha_seleccionada = mysqli_real_escape_string($conexion, $_POST['fecha']);
    $stmt = $conexion->prepare("INSERT INTO hacienda (fecha) VALUES (?)");
    $stmt->bind_param("s", $fecha_seleccionada);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    $stmt->close();
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de Fecha para la Hacienda</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Selecciona la Fecha para tu Evento en la Hacienda</h2>
    <div class="row">
        <div class="col-md-6">
            <form id="fechaForm">
                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha del Evento</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                </div>
                <div class="mb-3">
                    <button type="button" id="comprobarDisponibilidad" class="btn btn-primary">Comprobar Disponibilidad</button>
                    <button type="button" id="realizarReserva" class="btn btn-success" style="display:none;">Realizar Reserva</button>
                </div>
            </form>
        </div>
    </div>
    <div id="resultado" class="alert" style="display: none;"></div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<!-- jQuery (necesario para AJAX) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    
$(document).ready(function() {
    $('#comprobarDisponibilidad').click(function() {
        var fechaSeleccionada = $('#fecha').val();
        $.ajax({
            url: 'comprobar_fecha.php', // El script PHP que manejará la solicitud
            type: 'POST',
            dataType: 'json',
            data: { action: 'check_date', fecha: fechaSeleccionada },
            success: function(response) {
                $('#resultado').show();
                if(response.status === 'available') {
                    $('#resultado').addClass('alert-success').removeClass('alert-danger').text('¡Fecha disponible! Puedes proceder con la reserva.');
                    $('#realizarReserva').show(); // Mostrar el botón de reserva
                } else {
                    $('#resultado').addClass('alert-danger').removeClass('alert-success').text('Fecha no disponible, por favor elige otra.');
                    $('#realizarReserva').hide(); // Ocultar el botón de reserva
                }
            },
            error: function() {
                $('#resultado').show().addClass('alert-danger').text('Hubo un error al comprobar la fecha.');
            }
        });
    });

    $('#realizarReserva').click(function() {
        var fechaSeleccionada = $('#fecha').val();
        $.ajax({
            url: 'Comprobar_fecha.php', //
            type: 'POST',
            dataType: 'json',
            data: { action: 'save_date', fecha: fechaSeleccionada },
            success: function(response) {
                if(response.status === 'success') {
                    $('#resultado').addClass('alert-success').removeClass('alert-danger').text('¡Reserva realizada con éxito!');
                } else {
                    $('#resultado').addClass('alert-danger').removeClass('alert-success').text('No se pudo realizar la reserva.');
                }
            },
            error: function() {
                $('#resultado').addClass('alert-danger').text('Hubo un error al realizar la reserva.');
            }
        });
    });
});
</script>

</body>
</html>
