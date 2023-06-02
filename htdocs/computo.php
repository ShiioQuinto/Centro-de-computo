<?php
session_start();

// Datos de ejemplo de usuarios almacenados en una base de datos o arreglo
$usuarios = isset($_SESSION["usuarios"]) ? $_SESSION["usuarios"] : array(
    array("usuario" => "rocio", "password" => "123"),
    array("usuario" => "usuario1", "password" => "pass123"),
    array("usuario" => "usuario2", "password" => "pass456")
);

// Función para verificar el inicio de sesión
function iniciarSesion($usuario, $contraseña) {
    global $usuarios;

    foreach ($usuarios as $usr) {
        if ($usr["usuario"] === $usuario && $usr["password"] === $contraseña) {
            return true;
        }
    }

    return false;
}

// Función para dar de baja a un usuario
function darDeBajaUsuario($usuario) {
    global $usuarios;

    foreach ($usuarios as $key => $usr) {
        if ($usr["usuario"] === $usuario) {
            unset($usuarios[$key]);
            $_SESSION["usuarios"] = $usuarios; // Actualizar el arreglo en la sesión
            return true;
        }
    }

    return false;
}

function darDeAltaUsuario($usuario, $contraseña) {
    global $usuarios;

    foreach ($usuarios as $usr) {
        if ($usr["usuario"] === $usuario) {
            return false;
        }
    }

    $nuevoUsuario = array("usuario" => $usuario, "password" => $contraseña);
    $usuarios[] = $nuevoUsuario;
    $_SESSION["usuarios"] = $usuarios; // Almacena el arreglo actualizado en la sesión

    return true;
}


// Función para modificar los datos de un usuario
function modificarUsuario($usuario, $nuevoUsuario, $nuevaContraseña) {
    global $usuarios;

    foreach ($usuarios as &$usr) {
        if ($usr["usuario"] === $usuario) {
            $usr["usuario"] = $nuevoUsuario;
            $usr["password"] = $nuevaContraseña;
            return true;
        }
    }

    return false;
}

// Comprobar si el formulario de inicio de sesión se envió
if (isset($_POST["login"])) {
    $usuario = $_POST["usuario"];
    $contraseña = $_POST["contraseña"];

    // Verificar el inicio de sesión
    if (iniciarSesion($usuario, $contraseña)) {
        $_SESSION["usuario"] = $usuario;
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
}

// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION["usuario"])) {
    $usuarioActual = $_SESSION["usuario"];
    echo '<html>
        <head>
            <style>
                body {
                    font-family: Arial;
                    font-size: 18px;
                    background: linear-gradient(to bottom right, #FFD8E6, #E6FFD8);
                    padding: 20px;
                }
            </style>
        </head>
        <body>';
    echo "Bienvenido, $usuarioActual!<br><br>";

    // Mostrar el menú
    echo "Menú:<br>";
    echo '<form method="POST" action="">';
    echo '<input type="hidden" name="opcion" value="1">';
    echo '<input type="submit" value="Alta de usuario">';
    echo '</form>';
    echo '<form method="POST" action="">';
    echo '<input type="hidden" name="opcion" value="2">';
    echo '<input type="submit" value="Baja de usuario">';
    echo '</form>';
    echo '<form method="POST" action="">';
    echo '<input type="hidden" name="opcion" value="3">';
    echo '<input type="submit" value="Modificación de usuario">';
    echo '</form>';
    echo '<form method="POST" action="">';
    echo '<input type="hidden" name="opcion" value="4">';
    echo '<input type="submit" value="Mostrar usuarios">';
    echo '</form>';
    echo '<form method="POST" action="index.php">';
    echo '<input type="hidden" name="opcion" value="5">';
    echo '<input type="submit" value="Salir">';
    echo '</form>';

    // Comprobar la opción seleccionada
    if (isset($_POST["opcion"])) {
        $opcion = $_POST["opcion"];

        switch ($opcion) {
            case 1:
                echo "Seleccionaste la opción Alta de usuario.";
                // Mostrar formulario de alta de usuario
                echo '
                <form method="POST" action="">
                    Nuevo usuario: <input type="text" name="usuario_alta" required><br>
                    Contraseña: <input type="password" name="contraseña_alta" required><br>
                    <input type="submit" name="alta" value="Dar de alta">
                </form>';

                break;
            case 2:
                echo "Seleccionaste la opción Baja de usuario.";
                // Mostrar formulario de baja de usuario
                echo '
                <form method="POST" action="">
                    Usuario a dar de baja: <input type="text" name="usuario_baja" required><br>
                    <input type="submit" name="baja" value="Dar de baja">
                </form>';

                break;
            case 3:
                echo "Seleccionaste la opción Modificación de usuario.";
                // Mostrar formulario de modificación de usuario
                echo '
                <form method="POST" action="">
                    Usuario a modificar: <input type="text" name="usuario_modificar" required><br>
                    Nuevo usuario: <input type="text" name="nuevo_usuario" required><br>
                    Nueva contraseña: <input type="password" name="nueva_contraseña" required><br>
                    <input type="submit" name="modificar" value="Modificar usuario">
                </form>';

                break;
            case 4:
                echo "Seleccionaste la opción Mostrar usuarios.";
                // Mostrar lista de usuarios
                echo "Usuarios dados de alta:<br>";
                $usuariosMostrar = isset($_SESSION["usuarios"]) ? $_SESSION["usuarios"] : $usuarios; // Acceder al arreglo actualizado desde la sesión
                foreach ($usuariosMostrar as $usr) {
                    echo 'Usuario: ' . $usr["usuario"] . ' <form method="POST" action="">
                    <input type="hidden" name="opcion" value="2">
                    <input type="hidden" name="usuario_baja" value="' . $usr["usuario"] . '">
                    <input type="submit" name="baja" value="Dar de baja">
                    </form><br>';
                }

                break;

        }
    }

    // Comprobar si se envió el formulario de alta de usuario
    if (isset($_POST["alta"])) {
        $usuarioAlta = $_POST["usuario_alta"];
        $contraseñaAlta = $_POST["contraseña_alta"];

        // Dar de alta al usuario
        if (darDeAltaUsuario($usuarioAlta, $contraseñaAlta)) {
            echo "El usuario '$usuarioAlta' ha sido dado de alta.";
        } else {
            echo "El usuario '$usuarioAlta' ya existe.";
        }
    }

    // Comprobar si se envió el formulario de baja de usuario
    if (isset($_POST["baja"])) {
        $usuarioBaja = $_POST["usuario_baja"];

        // Dar de baja al usuario
        if (darDeBajaUsuario($usuarioBaja)) {
            echo "El usuario '$usuarioBaja' ha sido dado de baja.";
        } else {
            echo "El usuario '$usuarioBaja' no existe.";
        }
    }

    // Comprobar si se envió el formulario de modificación de usuario
    if (isset($_POST["modificar"])) {
        $usuarioModificar = $_POST["usuario_modificar"];
        $nuevoUsuario = $_POST["nuevo_usuario"];
        $nuevaContraseña = $_POST["nueva_contraseña"];

        // Modificar los datos del usuario
        if (modificarUsuario($usuarioModificar, $nuevoUsuario, $nuevaContraseña)) {
            echo "Los datos del usuario '$usuarioModificar' han sido modificados.";
        } else {
            echo "El usuario '$usuarioModificar' no existe.";
        }
    }
} else {
    // Formulario de inicio de sesión
    echo '<form method="POST" action="" style="font-family: Arial; font-size: 18px; background: linear-gradient(to right, #f6d365, #fda085);">
        Usuario: <input type="text" name="usuario" required><br>
        Contraseña: <input type="password" name="contraseña" required><br>
        <input type="submit" name="login" value="Iniciar sesión">
    </form>';
}
?>










