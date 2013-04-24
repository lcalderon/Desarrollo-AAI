<?
	include_once('../../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();
	$con->select_db($con->temporal);			

	
$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[DESCRIPCIONDELHECHO] = $_POST[DESCRIPCIONDELHECHO];
$reg[CONTRAPARTE] = $_POST[CONTRAPARTE];
$reg[RESULTADODELHECHO] = $_POST[RESULTADODELHECHO];
$reg[STATUSAFILIADO] = $_POST[STATUSAFILIADO];
$reg[RECOMENDACION] = $_POST[RECOMENDACION];
$reg[NUMERORECLAMO] = $_POST[NUMERORECLAMO];
$reg[NOMBREAJUSTADOR] = $_POST[NOMBREAJUSTADOR];

$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist('asistencia_vehicular_asesorialegal','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
{
	$con->update('asistencia_vehicular_asesorialegal',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
}
else 
{
	$con->insert_reg('asistencia_vehicular_asesorialegal',$reg);	
}



?>