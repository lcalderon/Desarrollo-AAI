<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();

$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[TIPOCONSULTA]=$_POST[ARRTIPOCONSULTA];

switch($reg[TIPOCONSULTA]){
	case 'TALL': $reg[SUBSERVICIO]=$_POST[REFTALLER];break;
	case 'RHOG': $reg[SUBSERVICIO]=$_POST[REFHOGAR];break;
}




$reg[RESULTADO] = $_POST[RESULTADO];
$reg[OTROS] = $_POST[OTROS];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_referencias_referenciavarios','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
{
	$con->update($con->temporal.'.asistencia_referencias_referenciavarios',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");	
}
else 
{
	$con->insert_reg($con->temporal.'.asistencia_referencias_referenciavarios',$reg);	
}

//print_r($_POST);

?>
