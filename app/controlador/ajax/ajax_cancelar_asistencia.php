<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();

//  actualiza la etapa en la tabla ASISTENCIA
$asis[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis[ARRSTATUSASISTENCIA]=$_POST[ARRSTATUSASISTENCIA];


//$asis[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

$con->update("$con->temporal.asistencia",$asis," WHERE IDASISTENCIA='$asis[IDASISTENCIA]'");

$asis_usuario[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis_usuario[IDUSUARIO]=$_POST[IDUSUARIOMOD];
$asis_usuario[IDETAPA]=$_POST[IDETAPA];

$con->insert_reg("$con->temporal.asistencia_usuario",$asis_usuario);


return;
?>