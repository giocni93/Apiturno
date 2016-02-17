<?php
//comprobamos que sea una petición ajax
if($_FILES["imagen"]["error"] <= 0) 
{
    //obtenemos el archivo a subir
    $file = $_FILES['imagen']['name'];
    //$file = $_GET['n'].".".$_GET['e'];
    
    //comprobamos si existe un directorio para subir el archivo
    //si no es así, lo creamos
    if(!is_dir("imagenes/")) {
        mkdir("imagenes/", 0777);
    }
    //comprobamos si el archivo ha subido
    if ($file && move_uploaded_file($_FILES['imagen']['tmp_name'],"imagenes/".$file))
    {
       sleep(3);//retrasamos la petición 3 segundos
       echo $file;//devolvemos el nombre del archivo para pintar la imagen
    }
}else{
   echo "Error Processing Request";   
}
