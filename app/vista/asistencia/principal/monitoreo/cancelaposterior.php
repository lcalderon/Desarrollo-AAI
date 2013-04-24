<?php
session_start();
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

$con->select_db($con->temporal);

$idasistencia = $_GET[idasistencia];
$idproveedor = $_GET[idproveedor];
$rows[ARRSTATUSASISTENCIA]='CP';
$resultado=$con->update("asistencia",$rows," WHERE IDASISTENCIA=".$idasistencia);
//echo "<script language='javascript'>alert($idproveedor)</script>";
/*echo 

$justificacion[IDASISTENCIAJUST]="";
$justificacion[IDUSUARIOMOD]=$_SESSION['user'];
$justificacion[IDJUSTIFICACION]=$idjustificacion;
$justificacion[IDASISTENCIA]=$idasistencia;
$justificacion[MOTIVO]=$observacion;
*/
/*echo $justificacion[IDUSUARIO];
echo $justificacion[IDJUSTIFICACION];
echo $justificacion[IDASISTENCIA];
echo $justificacion[OBSERVACION];*/

$rowasig_prov[STATUSPROVEEDOR]='CA';
$con->update("asistencia_asig_proveedor",$rowasig_prov," WHERE IDASISTENCIA=".$idasistencia." AND IDPROVEEDOR = ".$idproveedor);

$tarea[STATUSTAREA]='CANCELADA';
$con->update("monitor_tarea",$tarea," WHERE IDASISTENCIA=".$idasistencia." AND STATUSTAREA IN ('PENDIENTE','NO ATENDIDA')");
?>