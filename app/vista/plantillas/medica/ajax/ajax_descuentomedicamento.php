<?
include_once('../../../../modelo/clase_mysqli.inc.php');


$con = new DB_mysqli();

$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[SINTOMATOLOGIA] = $_POST[SINTOMATOLOGIA];
$reg[NOMBREMEDICAMENTO] = $_POST[NOMBREMEDICAMENTO];
$reg[PORCENTAJEDESCUENTO] = $_POST[PORCENTAJEDESCUENTO];
$reg[MONTOFACTURA] = $_POST[MONTOFACTURA];
$reg[OTROS] = $_POST[OTROS];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_medica_descuentomedicamento','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
{
	$con->update($con->temporal.'.asistencia_medica_descuentomedicamento',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
}
else 
{
	$con->insert_reg($con->temporal.'.asistencia_medica_descuentomedicamento',$reg);	
}

//print_r($_POST);

?>