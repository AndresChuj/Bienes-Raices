<?php

function conectarDB() : mysqli {
    $db = mysqli_connect('localhost', 'root', 'root', 'bienesraices_crud');

    if(!$db){
        echo 'Error no se puede ejecutar';
        exit;
    }

    return $db;
}