<?php
session_start();
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

$con->select_db($con->temporal);

$idproveedor = $_GET[idproveedor];

$idasistencia = $_GET[idasistencia];
$rows[IDETAPA]=2;
$rows[arrprioridadatencion]='EME';

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
$resultado=$con->update("asistencia",$rows," WHERE IDASISTENCIA=".$idasistencia);

$rowasig_prov[STATUSPROVEEDOR]='CA';
$con->update("asistencia_asig_proveedor",$rowasig_prov," WHERE IDASISTENCIA=".$idasistencia." AND IDPROVEEDOR = ".$idproveedor);

$tarea[STATUSTAREA]='CANCELADA';
$con->update("monitor_tarea",$tarea," WHERE IDASISTENCIA=".$idasistencia." AND STATUSTAREA='PENDIENTE'");

$sql_delete_ranking="delete from asistencia_ranking WHERE IDASISTENCIA = $idasistencia";
$exec_delete_ranking = $con->query($sql_delete_ranking);

echo "<script language='javascript'>";

echo "parent.top.document.location.href = '../../plantillas/etapa2.php?idasistencia=$idasistencia';";
	
	 echo "</script>";

?>