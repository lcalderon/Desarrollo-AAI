<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();


$asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis_bitacora[ARRCLASIFICACION]=$_POST[ARRCLASIFICACION];
$asis_bitacora[COMENTARIO]=$_POST[COMENTARIO];
$asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];
$asis_bitacora[IDPROVEEDOR]=$_GET[idproveedor];
$con->insert_reg("$con->temporal.asistencia_bitacora_etapa7",$asis_bitacora);



?>