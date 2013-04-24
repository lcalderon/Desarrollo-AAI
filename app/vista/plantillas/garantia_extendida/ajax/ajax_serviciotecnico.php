<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();

$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[DESCRIPCIONPROBLEMA] = $_POST[DESCRIPCIONPROBLEMA];
$reg[RESULTADOPROBLEMA] = $_POST[RESULTADOPROBLEMA];
$reg[RECOMENDACION] = $_POST[RECOMENDACION];
$reg[OBSERVACION] = $_POST[OBSERVACION];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_garantia_extendida_serviciotecnico','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
{
	$con->update($con->temporal.'.asistencia_garantia_extendida_serviciotecnico',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
}
else 
{
	$con->insert_reg($con->temporal.'.asistencia_garantia_extendida_serviciotecnico',$reg);	
}

//print_r($_POST);

?>