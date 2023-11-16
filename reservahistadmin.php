<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'roles';

$conex = mysqli_connect($host, $username, $password, $database);

if (!$conex) {
    die("Connection failed: " . mysqli_connect_error());
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    // Si no hay sesión, redirigir al formulario de inicio de sesión
    header('Location: index.php');
    exit();
}

$usuario = $_SESSION['usuario'];
$es_admin_o_coordinador = isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['admin', 'coordinador']);

// Si es admin o coordinador, permitir ver el historial de cualquier usuario
if ($es_admin_o_coordinador && isset($_GET['usuario'])) {
    $usuario = $_GET['usuario']; // El nombre de usuario se pasa como parámetro GET
}

$archivo_pedidos = __DIR__ . "/pedidos/" . $usuario . "_pedidos.json";

$pedidos = [];
if (file_exists($archivo_pedidos)) {
    $json_data = file_get_contents($archivo_pedidos);
    $pedidos = json_decode($json_data, true); // Convertir JSON a array PHP

    // Verificar si la decodificación fue exitosa
    if (is_null($pedidos)) {
        // Manejar el error, por ejemplo, registrarlo o mostrar un mensaje
        error_log("Error al decodificar el JSON del archivo: " . $archivo_pedidos);
        $pedidos = []; // Asegurarse de que $pedidos es un arreglo
    }
} else {
    // Manejar el caso de que el archivo no exista
    error_log("El archivo de pedidos no existe: " . $archivo_pedidos);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pedidos</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 56px; /* Espacio para la barra de navegación fija */
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .table-responsive {
            margin-top: 20px;
        }
        .navbar-brand {
            font-weight: bold;
            color: black;
        }
        .nav-link {
            color: black;
        }
        .nav-link:hover {
            color: black;
        }
        .bg-custom {
            background-color: black;
        }
        .thead-custom {
            background-color: #0056b3;
            color: #fff;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-custom fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="Admin.php">Volver atrás</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                </li>
            </ul>
        </div>
    </div>
</nav>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Historial de Pedidos de <?php echo htmlspecialchars($usuario); ?></h2>
        <?php if (!empty($pedidos)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-custom">
                        <tr>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>Productos</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pedido['folio'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($pedido['fecha'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($pedido['email'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($pedido['direccion'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php foreach ($pedido['productos'] as $producto): ?>
                                        <p><?php echo htmlspecialchars($producto['nombre']); ?> - <?php echo htmlspecialchars($producto['cantidad']); ?> x $<?php echo htmlspecialchars($producto['precio']); ?> (Subtotal: $<?php echo htmlspecialchars($producto['subtotal']); ?>)</p>
                                    <?php endforeach; ?>
                                </td>
                                <td>$<?php echo number_format($pedido['total'] ?? 0, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center">No hay historial de pedidos para mostrar.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5 JS y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
