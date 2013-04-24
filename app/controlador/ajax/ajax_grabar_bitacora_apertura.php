<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();



$asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis_bitacora[COMENTARIO]=$_POST[COMENTARIO];
$asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];
$asis_bitacora[ARRCLASIFICACION]=$_POST[ARRCLASIFICACION];

$con->insert_reg("$con->temporal.asistencia_bitacora_etapa1",$asis_bitacora);

?>