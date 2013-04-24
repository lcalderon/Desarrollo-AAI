<?php
session_start();
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

$con->select_db($con->temporal);

$idasistencia = $_POST[hid_idasistenciacancela];
$rows[ARRSTATUSASISTENCIA]='CM';
$resultado=$con->update("asistencia",$rows," WHERE IDASISTENCIA=".$idasistencia);
//echo "<script language='javascript'>alert($idasistencia)</script>";
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


$tarea[STATUSTAREA]='CANCELADA';
$con->update("monitor_tarea",$tarea," WHERE IDASISTENCIA=".$idasistencia." AND STATUSTAREA='PENDIENTE'");
 /*
	echo "<script>";
	if(!$respuesta)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');"; 
	//echo "document.location.href='proveedor_asignado.php?idasist=$asigprov[IDASISTENCIA]'";
	 echo "</script>";	

*/
/*
echo "<script language='javascript'>alert('Proveedor Asignado con Exito'); window.location.href= 'proveedor_asignado.php?idasist='+$asigprov[IDASISTENCIA];</script>";	
*/

//header('location: proveedor_asignado.php?idasist='.$asigprov[NUMASISTENCIA]);

?>