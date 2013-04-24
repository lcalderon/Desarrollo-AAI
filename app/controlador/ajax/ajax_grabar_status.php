<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();

//  actualiza la etapa en la tabla ASISTENCIA
$asis[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis[ARRSTATUSASISTENCIA]=$_POST[ARRSTATUSASISTENCIA];


$con->update("$con->temporal.asistencia",$asis," WHERE IDASISTENCIA='$asis[IDASISTENCIA]'");

//if($_POST[ARRSTATUSASISTENCIA]=='CON'){
//    $asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
//    $asis_bitacora[COMENTARIO]='ASISTENCIA CONCLUIDA';
//    $asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];
//
//    $con->insert_reg("$con->temporal.asistencia_bitacora_etapa8",$asis_bitacora);
//}
//
//$tarea[STATUSTAREA]='CANCELADA';
//$con->update("$con->temporal.monitor_tarea",$tarea," WHERE IDASISTENCIA='$asis[IDASISTENCIA]' AND STATUSTAREA='PENDIENTE'");
//
//$tarea2[DISPLAY]=0;
//$con->update("$con->temporal.monitor_tarea",$tarea2," WHERE IDASISTENCIA='$asis[IDASISTENCIA]' AND STATUSTAREA IN ('NO ATENDIDA','ABANDONO','ATENDIDA CON RETRASO')");

return;
?>