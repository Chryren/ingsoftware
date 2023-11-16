<?php
// comprobar_fecha.php

session_start();

// Conectar a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "roles");

if (!$conexion) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
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
