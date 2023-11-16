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

// Inicializar mensaje de confirmación
$_SESSION['mensaje'] = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['btnAccion']) && $_POST['btnAccion'] == 'agrega') {
        $nombre_producto = $_POST['nombre'];
        $descrip_producto = $_POST['descrip'];
        $precio_producto = $_POST['precio'];

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $producto_encontrado = array_search($nombre_producto, array_column($_SESSION['carrito'], 'nombre'));
        if ($producto_encontrado === false) {
            $_SESSION['carrito'][] = [
                'nombre' => $nombre_producto,
                'descrip' => $descrip_producto,
                'precio' => $precio_producto,
                'cantidad' => 1
            ];
            $_SESSION['mensaje'] = 'Producto añadido al carrito';
        } else {
            $_SESSION['mensaje'] = 'El producto ya está en el carrito';
        }
    }

    // Manejar la acción de eliminar del carrito
    if (isset($_POST['btnAccion']) && $_POST['btnAccion'] == 'elimina') {
        $nombre_producto = $_POST['nombre'];
        foreach ($_SESSION['carrito'] as $indice => $producto) {
            if ($producto['nombre'] == $nombre_producto) {
                unset($_SESSION['carrito'][$indice]);
                $_SESSION['mensaje'] = 'Producto eliminado del carrito';
                break;
            }
        }
    }
}

$consulta = "SELECT * FROM menu";
$resultado = mysqli_query($conex, $consulta);
// Calcular el total de ítems en el carrito
$total_items = count($_SESSION['carrito'] ?? []);


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="CSS/stylec.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Mensaje de confirmación -->
<?php if(!empty($_SESSION['mensaje'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['mensaje']; ?>
    </div>
<?php endif; ?>


<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="Visualizar2.php">Menú Xtepén</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="Cliente.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ver_carrito.php">Carrito<span class="badge bg-secondary"><?php echo $total_items; ?></span></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Productos -->
<div class="container mt-4">
    <div class="row">
        <?php while($producto = mysqli_fetch_assoc($resultado)): ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $producto['nombre']; ?></h5>
                        <p class="card-text"><?php echo $producto['descrip']; ?></p>
                        <p class="card-text"><?php echo $producto['precio']; ?></p>
                        <form action="" method="post">
                            <input type="hidden" name="nombre" value="<?php echo $producto['nombre']; ?>">
                            <input type="hidden" name="descrip" value="<?php echo $producto['descrip']; ?>">
                            <input type="hidden" name="precio" value="<?php echo $producto['precio']; ?>">
                            <button class="btn btn-primary" name="btnAccion" value="agrega" type="submit">Añadir al carrito</button>
                            <button class="btn btn-danger" name="btnAccion" value="elimina" type="submit">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
