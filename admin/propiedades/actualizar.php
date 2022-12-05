<?php
require '../../includes/funciones.php';
$auth = estadoAutenticado();

if(!$auth){
    header('location: /');
}

//validar la url por id valido
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if(!$id){
    header('location: /admin');
}


//conectar base de datos
require '../../includes/config/database.php';
$db = conectarDB();


//obtener los datos de la propiedad
$consulta = "SELECT * FROM propiedades WHERE id = ${id}";
$resultado = mysqli_query($db, $consulta);
$propiedad = mysqli_fetch_assoc($resultado);

//consultar para obtener los vendedores
$consulta = "SELECT * FROM  vendedores";
$resultado = mysqli_query($db, $consulta);

//arreglo con mensaje de errores    
$errores = [];

$titulo = $propiedad['titulo'];
$precio = $propiedad['precio'];
$descripcion =$propiedad['descripcion'];
$habitaciones = $propiedad['habitaciones'];
$wc = $propiedad['wc'];
$estacionamiento = $propiedad['estacionamiento'];
$vendedores_Id = $propiedad['vendedores_id'];
$creado = date('y-m-d');
$propiedadImagen = $propiedad['imagen'];

//ejecutar el codigo despues que el usuario  envié  el formulario
if($_SERVER['REQUEST_METHOD'] === 'POST'){
        // echo "<pre>";
        // var_dump($_POST);
        // echo "</pre>";


    $titulo = mysqli_real_escape_string($db,$_POST['titulo']) ;
    $precio = mysqli_real_escape_string($db,$_POST['precio']) ;
    $descripcion = mysqli_real_escape_string($db,$_POST['descripcion']) ;
    $habitaciones = mysqli_real_escape_string($db,$_POST['habitaciones']) ;
    $wc = mysqli_real_escape_string($db,$_POST['wc']) ;
    $estacionamiento = mysqli_real_escape_string($db,$_POST['estacionamiento']) ;
    $vendedores_Id = mysqli_real_escape_string($db,$_POST['vendedores_Id']) ;

    //asignar files hacia una variable
    $imagen = $_FILES['imagen'];

    if(!$titulo){
        $errores[] = "debes añadir un titulo";
    }

    if(!$precio){
        $errores[] = "debes añadir un precio";
    }

    if(!$habitaciones){
        $errores[] = "el numero de habitacionenes es obligatorio";
    }

    if(!$wc){
        $errores[] = "el numero de baños es obligatorio";
    }

    if(!$estacionamiento){
        $errores[] = "el numero de lugares de estacionamientos es obligatorio";
    }

    if(!$vendedores_Id){
        $errores[] = "elige un  vendedor";
    }

    if(strlen($descripcion) < 50){
        $errores[] = "debes añadir una descripcion y tener al menos 50 caracteres";
    }



    //valiadar  por tamaño (1 mb maximo)
    $medida = 1000 * 1000;

    if($imagen['size'] > $medida){
        $errores[] = 'la imagen es muy pesada';
    }

   //revisar que el arreglo de errores  este vacio

    if(empty($errores)){

          //crear una  carpeta
            $carpetaImagenes = '../../imagenes/';

            if(!is_dir($carpetaImagenes)){
                   mkdir($carpetaImagenes);
               }
           //subida  de archivos
           if($imagen['name']){
            //eliminar la imagen previa
            unlink($carpetaImagenes.$propiedad['imagen']);

             //generar un nombre un unico
             $nombreImagen = md5( uniqid( rand(), true ) ).'.jpg';

             //subir la imagen
              move_uploaded_file($imagen['tmp_name'], $carpetaImagenes.$nombreImagen);
           } else {
                $nombreImagen = $propiedad['imagen'];
           }





    //        



            $query = " UPDATE propiedades SET titulo = '${titulo}', precio = '${precio}', imagen = '${nombreImagen}', descripcion = '${descripcion}', habitaciones = ${habitaciones}, wc = ${wc}, estacionamiento = ${estacionamiento}, vendedores_Id = ${vendedores_Id} WHERE id = ${id} ";

         

            

        $resultado = mysqli_query($db, $query);

        if($resultado){
            header('location: /admin?resultado=2');
        }
    }
}



incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>actualizar propiedad</h1>

        <a href="/admin" class="boton boton-verde">volver</a>

        <?php foreach($errores as $error): ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
        <?php endforeach;?>

        <form  method="POST" class="formulario"
        enctype="multipart/form-data">
            <fieldset>
                <legend>información general</legend>

                <label for="titulo">titulo</label>
                <input type="text" id="titulo" name="titulo" placeholder="titulo de la propiedad" value="<?php echo $titulo ?>" >

                <label for="precio">precio</label>
                <input type="number" id="precio" name="precio" placeholder="precio propiedad" value="<?php echo $precio ?>" >

                <label for="imagen">imagen</label>
                <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">
                <img src="/imagenes/<?php echo $propiedadImagen ?>" alt="" class="imagen-small">

                <label for="descripcion">descripción</label>
                <textarea name="descripcion" id="descripcion">  <?php echo $descripcion ?> </textarea>
                
            </fieldset>

            <fieldset>
                <legend>información propiedad</legend>

                <label for="habitaciones">habitaciones:</label>
                <input type="number" name="habitaciones" id="habitaciones" placeholder="ej: 3" min="1" max="9" value="<?php echo $habitaciones ?>" >

                <label for="wc">baños:</label>
                <input type="number" id="wc" name="wc" placeholder="ej: 3" min="1" max="9"  value="<?php echo $wc ?>" >

                <label for="estacionamiento">estacionamietos:</label>
                <input type="number" id="estacionamiento" name="estacionamiento" placeholder="ej: 3" min="1" max="9"  value="<?php echo $estacionamiento ?>" >

            </fieldset>

            <fieldset>
                <legend>vededor</legend>
                <select name="vendedores_Id" id="vendedores_Id" >
                <?php echo $vendedores_Id ?>
                    <option value=" " disabled selected>seleccione</option>
                    <?php while($vendedor = mysqli_fetch_assoc($resultado)): ?>
                        <option <?php echo $vendedores_Id === $vendedor['id'] ? 'selected' : ''; ?> value="<?php echo $vendedor['id'];  ?>" >
                        <?php echo $vendedor['nombre']." ".$vendedor['apellido'];?>
                        </option>

                    <?php endwhile;  ?> 
                </select>
            </fieldset>
            <input type="submit" value="actualizar propiedad" class="boton boton-verde">
        </form>
    </main>

    <?php
        incluirTemplate('footer');
    ?>