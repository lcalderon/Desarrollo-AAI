<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();


$con->borrar_reg("$con->temporal.asistencia_disponibilidad_afiliado"," ID =$_POST[IDDISPO]");

?>