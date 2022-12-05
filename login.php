<?php
require 'includes/config/database.php';
$db = conectarDB();

$errores = [];


//autenticar el usuario
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // echo "<pre>";
    // var_dump($_POST);
    // echo "</pre>";

    $email = mysqli_real_escape_string($db, filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL));

    $password = mysqli_real_escape_string($db, $_POST['password'] );

    if(!$email){
        $errores[] = "el email es obligatorio o no es valido";
    }

    if(!$password){
        $errores[] = "el password es obligatorio o no es valido";
    }

    if(empty($errores)){
        //revisar si el  usuario existe
        $query = "SELECT * FROM usuarios WHERE email = '${email}' ";
        $resultado = mysqli_query($db, $query);

        if($resultado->num_rows){
            //revisar si el password es correcto
            $usuario = mysqli_fetch_assoc($resultado);

            //revisar si el  password es correcto o no
            $auth = password_verify($password, $usuario['password']);

            if($auth){
                //el usuario esta  autorizado
                session_start();

                //llenar el arreglo de  la  sesion
                $_SESSION['usuario'] = $usuario['email'];
                $_SESSION['login'] = true;

                header('location: /admin');



            }else{
                $errores[] = 'El password es incorrecto';
            }

        }else{
            $errores[] = 'el correo no existe';
        }
    }

}

//incluyer el  header
require 'includes/funciones.php';

incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Iniciar Sesión</h1>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>

        <form method="POST" action="" class="formulario" class="formulario-centrado">
        <fieldset>
                <legend>Email y Password</legend>

                <label for="email">E-mail</label>
                <input type="email" name="email" placeholder="Tu Email" id="email">

                <label for="password">password</label>
                <input type="password" name="password" placeholder="Tu password" id="password">
            </fieldset>

            <input type="submit" value="inciar Sesión" class="boton boton-verde">
        </form>
    </main>

    <?php
        incluirTemplate('footer');
    ?>