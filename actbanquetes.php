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

// Consulta para obtener la información actual de los banquetes
$consulta = "SELECT * FROM banquetes";
$resultado = mysqli_query($conex, $consulta);

// Verificar si el formulario de actualización fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btnActualizar'])) {
    $id_banquete = $_POST['id_banquete'];
    $nuevo_nombreb = $_POST['nuevo_nombreb'];
    $nueva_descripb = $_POST['nueva_descripb'];
    $nuevo_preciob = $_POST['nuevo_preciob'];

    // Realizar la actualización en la base de datos
    $actualizar_consulta = "UPDATE banquetes SET nombreb = '$nuevo_nombreb', descripb = '$nueva_descripb', preciob = '$nuevo_preciob' WHERE id_banquetes = $id_banquete";
    if (mysqli_query($conex, $actualizar_consulta)) {
        $_SESSION['mensaje'] = 'Banquete actualizado con éxito';
        // Redirigir a la página actual para actualizar la vista
        header("Location: $_SERVER[PHP_SELF]");
        exit();
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar el banquete: ' . mysqli_error($conex);
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
    <h2>Banquetes</h2>
    <table class="table">
        <!-- ... -->
        <tbody>
        <?php while ($banquete = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?php echo htmlspecialchars($banquete['nombreb']); ?></td>
                <td><?php echo htmlspecialchars($banquete['descripb']); ?></td>
                <td><?php echo htmlspecialchars($banquete['preciob']); ?></td>
                <td>
                    <!-- Botón para abrir el formulario de actualización -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#actualizarModal<?php echo $banquete['id_banquetes']; ?>">Actualizar</button>
                </td>
            </tr>

            <!-- Modal de actualización -->
            <div class="modal fade" id="actualizarModal<?php echo $banquete['id_banquetes']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Actualizar Banquete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="post">
                                <input type="hidden" name="id_banquete" value="<?php echo htmlspecialchars($banquete['id_banquetes']); ?>">
                                <div class="mb-3">
                                    <label for="nuevo_nombreb" class="form-label">Nuevo Nombre</label>
                                    <input type="text" class="form-control" id="nuevo_nombreb" name="nuevo_nombreb" value="<?php echo htmlspecialchars($banquete['nombreb']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="nueva_descripb" class="form-label">Nueva Descripción</label>
                                    <textarea class="form-control" id="nueva_descripb" name="nueva_descripb"><?php echo htmlspecialchars($banquete['descripb']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="nuevo_preciob" class="form-label">Nuevo Precio</label>
                                    <input type="text" class="form-control" id="nuevo_preciob" name="nuevo_preciob" value="<?php echo htmlspecialchars($banquete['preciob']); ?>" pattern="\d+(\.\d{2})?" title="Ingrese un precio válido (ejemplo: 10.99)" required>
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
