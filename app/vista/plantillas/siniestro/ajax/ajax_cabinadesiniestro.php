<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();

$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[DESCRIPCIONDELHECHO] = $_POST[DESCRIPCIONDELHECHO];
$reg[DANIO] = $_POST[DANIO];
$reg[TRAMITE] = $_POST[TRAMITE];
$reg[OTRO] = $_POST[OTRO];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_siniestro_cabinadesiniestro','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
{
	$con->update($con->temporal.'.asistencia_siniestro_cabinadesiniestro',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
}
else 
{
	$con->insert_reg($con->temporal.'.asistencia_siniestro_cabinadesiniestro',$reg);	
}

var_dump($reg);

?>