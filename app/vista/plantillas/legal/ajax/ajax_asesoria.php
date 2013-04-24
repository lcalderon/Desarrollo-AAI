<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();

$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[DESCRIPCIONDELHECHO] = $_POST[DESCRIPCIONDELHECHO];
$reg[CONTRAPARTE] = $_POST[CONTRAPARTE];
$reg[RESULTADODELHECHO] = $_POST[RESULTADODELHECHO];
$reg[STATUSAFILIADO] = $_POST[STATUSAFILIADO];
$reg[RECOMENDACION] = $_POST[RECOMENDACION];

$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_legal_asesoria','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
{
	$con->update($con->temporal.'.asistencia_legal_asesoria',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
}
else 
{
	$con->insert_reg($con->temporal.'.asistencia_legal_asesoria',$reg);	
}



?>