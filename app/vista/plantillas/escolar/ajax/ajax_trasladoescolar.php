<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();

$horarioida=$_POST["dateesc1"]." ".$_POST["dcbhora1"].":".$_POST["dcbminuto1"];
$horarioregreso=$_POST["dateesc2"]." ".$_POST["dcbhora2"].":".$_POST["dcbminuto2"];

$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[IDUBIGEODESTINO] = $_POST[IDDESTINO];
$reg[HORARIOIDA] = $horarioida;
$reg[HORARIOVUELTA] = $horarioregreso;
$reg[OTROS] = $_POST[txtaotros];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_escolar_trasladoescolar','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
{
	$con->update($con->temporal.'.asistencia_escolar_trasladoescolar',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
}
else 
{
	$con->insert_reg($con->temporal.'.asistencia_escolar_trasladoescolar',$reg);	
}

?>