<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();

$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[DESCRIPCIONDELHECHO] = $_POST[DESCRIPCIONDELHECHO];
$reg[ARRRAMA] = $_POST[ARRRAMA];
$reg[CONTRAPARTE] = $_POST[CONTRAPARTE];
$reg[PRETENCION] = $_POST[PRETENCION];
$reg[RECOMENDACION] = $_POST[RECOMENDACION];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_legal_consultoria','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
{
	$con->update($con->temporal.'.asistencia_legal_consultoria',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
}
else 
{
	$con->insert_reg($con->temporal.'.asistencia_legal_consultoria',$reg);	
}

var_dump($reg);

?>