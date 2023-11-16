<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$total = 0;

// Verificar si el carrito existe y no está vacío
if (!isset($_SESSION['carrito']) || count($_SESSION['carrito']) == 0) {
    // Asegurarse de que no estamos ya en la página ver_carrito.php antes de redirigir
    if (basename($_SERVER['PHP_SELF']) != 'ver_carrito.php') {
        $_SESSION['error'] = 'Tu carrito está vacío. Agrega algunos productos antes de procesar el pedido.';
        header('Location: ver_carrito.php');
        exit;
    } else {
        // Estamos en ver_carrito.php, mostrar mensaje de error aquí
        $error = 'Tu carrito está vacío. Agrega algunos productos antes de continuar.';
    }
}

foreach ($_SESSION['carrito'] as $indice => $producto) {
    // Verificar si las claves existen antes de usarlas
    if (isset($producto['precio']) && isset($producto['cantidad'])) {
        $precio_limpio = preg_replace('/[^0-9.]+/', '', $producto['precio']);
        $precio = floatval($precio_limpio);
        $subtotal = $precio * $producto['cantidad'];
        $total += $subtotal;
    } else {
        // Manejar el caso en que no existan las claves 'precio' o 'cantidad'
        $_SESSION['error'] = 'Hay un error con uno de los productos en tu carrito.';
        // Considerar eliminar el producto problemático del carrito o ignorarlo
        // unset($_SESSION['carrito'][$indice]);
    }
}
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Carrito</title>
    <link rel="stylesheet" href="CSS/stylec.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Menú Xtepén</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="Cliente.php">Volver atrás</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-black">Carrito de Compras</h2>
    <?php if(isset($error)): ?>
        <div class="alert alert-warning">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <?php if(!empty($_SESSION['mensaje'])): ?>
        <div class="alert alert-info">
            <?php echo $_SESSION['mensaje']; ?>
        </div>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        ?>
<tbody>
    <?php foreach ($_SESSION['carrito'] as $indice => $producto): ?>
        <?php
        // Verificar si las claves de banquete existen antes de usarlas
        if (isset($producto['preciob']) && isset($producto['nombreb']) && isset($producto['cantidad'])) {
            $precio_limpio = preg_replace('/[^0-9.]+/', '', $producto['preciob']);
            $precio = floatval($precio_limpio);
            $nombre = htmlspecialchars($producto['nombreb']);
        // Verificar si las claves de menú existen antes de usarlas
        } elseif (isset($producto['precio']) && isset($producto['nombre']) && isset($producto['cantidad'])) {
            $precio_limpio = preg_replace('/[^0-9.]+/', '', $producto['precio']);
            $precio = floatval($precio_limpio);
            $nombre = htmlspecialchars($producto['nombre']);
        } else {
            // Manejar el caso en que no existan las claves esperadas
            $subtotal = 0;
            $nombre = "Error: Producto no identificado";
            continue; // Salta este producto y continúa con el siguiente
        }
        $subtotal = $precio * $producto['cantidad'];
        $total += $subtotal; // Acumula el subtotal en el total
        ?>
        <tr>
            <td><?php echo $nombre; ?></td>
            <td>$<?php echo number_format($precio, 2); ?></td>
            <td><?php echo $producto['cantidad']; ?></td>
            <td>$<?php echo number_format($subtotal, 2); ?></td>
        </tr>
        <?php endforeach; ?>
    <tr>
        <td colspan="3" class="text-right"><strong>Total</strong></td>
        <td>$<?php echo number_format($total, 2); ?></td>
    </tr>
</tbody>
    </table>

    <h3 class="text-white">Ingresar datos</h3>
    <form action="procesar_pedido.php" method="post">
        <div class="mb-3">
            <label for="nombre" class="form-label text-white">Nombre completo</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label text-white">Correo electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label text-white">Dirección de envío</label>
            <input type="text" class="form-control" id="direccion" name="direccion" required>
        </div>
        <button type="submit" class="btn btn-primary">Realizar pedido</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
