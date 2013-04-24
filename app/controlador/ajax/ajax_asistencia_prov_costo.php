<?
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_asistencia.inc.php');

$asistencia = new asistencia();
$asistencia->graba_asist_asig_prov_costo($_POST);
$asistencia->act_asistencia_costo($_POST[IDASISTENCIA]);
// ACTUALIZA EL NOMBRE DEL USUARIO QUE AUTORIZA LA ASISTENCIA//
if ($_POST[MODO]=='AUTORIZA') {
	$reg[IDUSUARIOAUTORIZADESVIO]=$_POST[IDUSUARIOMOD];
	$reg[FECHAAUTORIZADESVIO]=date("Y-m-d H:i:s",time());
	$asistencia->update("$asistencia->temporal.asistencia",$reg," WHERE IDASISTENCIA='$_POST[IDASISTENCIA]'");
}
//echo $_POST[IDASISTENCIA];

?>