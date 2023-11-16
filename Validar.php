<?php
if (isset($_POST['usuario']) && isset($_POST['contraseña'])) {
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];

    session_start();

    $conexion = mysqli_connect("localhost", "root", "", "roles");

    if (!$conexion) {
        die("Error al conectar a la base de datos: " . mysqli_connect_error());
    }

    // Evitar inyecciones SQL
    $usuario = mysqli_real_escape_string($conexion, $usuario);
    $contraseña = mysqli_real_escape_string($conexion, $contraseña);

    $consulta = "SELECT * FROM usuarios WHERE usuario='$usuario' AND contraseña='$contraseña'";
    $resultado = mysqli_query($conexion, $consulta);

    if (!$resultado) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    $filas = mysqli_fetch_assoc($resultado);

    if ($filas) {
        // Almacenar el usuario y el nombre en la sesión
        $_SESSION['usuario'] = $usuario;
        $_SESSION['nombre_usuario'] = $filas['nombre']; // Asegúrate de que 'nombre' sea el nombre de la columna en tu base de datos

        switch ($filas['id_cargo']) {
            case 1: // Administrador
                header("location: Admin.php");
                break;
            case 2: // Coordinador
                header("location: Coordinador.php");
                break;
            case 3: // Cliente
                header("location: Cliente.php");
                break;
            default:
                echo "Error en la autenticación";
                break;
        }
    } else {
        echo "Usuario o contraseña incorrectos, favor de volver a ingresar sus credenciales";
    }

    mysqli_free_result($resultado);
    mysqli_close($conexion);
}
?>
