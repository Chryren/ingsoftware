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

// Consulta para obtener la información actual de los productos
$consulta = "SELECT * FROM menu";
$resultado = mysqli_query($conex, $consulta);

// Verificar si el formulario de actualización fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnActualizar'])) {
    $id_producto = $_POST['id_producto'];
    $nuevo_nombre = $_POST['nuevo_nombre'];
    $nueva_descripcion = $_POST['nueva_descripcion'];
    $nuevo_precio = $_POST['nuevo_precio'];

    // Realizar la actualización en la base de datos
    $actualizar_consulta = "UPDATE menu SET nombre = '$nuevo_nombre', descrip = '$nueva_descripcion', precio = '$nuevo_precio' WHERE menu_id = $id_producto";
    if (mysqli_query($conex, $actualizar_consulta)) {
        $_SESSION['mensaje'] = 'Producto actualizado con éxito';
        // Redirigir a la página actual para actualizar la vista
        header("Location: $_SERVER[PHP_SELF]");
        exit();
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar el producto: ' . mysqli_error($conex);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Administración</title>
    <link rel="stylesheet" href="CSS/stylec.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Mensaje de confirmación -->
<?php if (!empty($_SESSION['mensaje'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['mensaje']; ?>
    </div>
<?php endif; ?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Dashboard de Administración</a>
        <a class="navbar-brand" href="Admin.php">Volver atrás</a>
    </div>
</nav>

<div class="container mt-4">
    <h2>Productos</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($producto = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?php echo $producto['nombre']; ?></td>
                <td><?php echo $producto['descrip']; ?></td>
                <td><?php echo $producto['precio']; ?></td>
                <td>
                    <!-- Botón para abrir el formulario de actualización -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#actualizarModal<?php echo $producto['menu_id']; ?>">Actualizar</button>
                </td>
            </tr>

            <!-- Modal de actualización -->
            <div class="modal fade" id="actualizarModal<?php echo $producto['menu_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Actualizar Producto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="post">
                                <input type="hidden" name="id_producto" value="<?php echo isset($producto['menu_id']) ? $producto['menu_id'] : ''; ?>">
                                <div class="mb-3">
                                    <label for="nuevo_nombre" class="form-label">Nuevo Nombre</label>
                                    <input type="text" class="form-control" id="nuevo_nombre" name="nuevo_nombre" value="<?php echo isset($producto['nombre']) ? $producto['nombre'] : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="nueva_descripcion" class="form-label">Nueva Descripción</label>
                                    <textarea class="form-control" id="nueva_descripcion" name="nueva_descripcion"><?php echo isset($producto['descrip']) ? $producto['descrip'] : ''; ?></textarea>
                                </div>
                                <div class="mb-3">
                                        <label for="nuevo_precio" class="form-label">Nuevo Precio</label>
                                        <input type="text" class="form-control" id="nuevo_precio" name="nuevo_precio" value="<?php echo isset($producto['precio']) ? $producto['precio'] : ''; ?>" pattern="\$\d+(\.\d{2})?" title="Ingrese un precio válido (ejemplo: $10.99)" required>
                                     </div>

                                <button type="submit" class="btn btn-primary" name="btnActualizar">Guardar Cambios</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    function recargarPagina() {
        setTimeout(function() {
            location.reload();
        }, 1000); // Recargar la página después de 1 segundo (1000 milisegundos)
    }
</script>
</body>
</html>
