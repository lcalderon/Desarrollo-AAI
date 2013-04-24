<?php 
include_once "../../modelo/clase_mysqli.inc.php";
$con = new DB_mysqli;

$sql ="select MIME,IMAGEN from $con->temporal.asistencia_imagenes where IDIMAGEN = '$_GET[IDIMAGEN]'";
$result = $con->query($sql);

$contenido = $result->fetch_object();

header("Content-type: ".$contenido->MIME);
echo $contenido->IMAGEN;
?>