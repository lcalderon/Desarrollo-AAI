<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();


$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[SINTOMATOLOGIA] = $_POST[SINTOMATOLOGIA];
$reg[ANTECEDENTESCLINICOS] = $_POST[ANTECEDENTESCLINICOS];
$reg[DIAGNOSTICO] = $_POST[DIAGNOSTICO];
$reg[DERIVACION] = $_POST[DERIVACION];
//$reg[IDLUGARORIGEN] = $_POST[IDLUGARORIGEN];
$reg[IDLUGARDESTINO] = $_POST[IDLUGARDESTINO];
$reg[RECOMENDACION] = $_POST[RECOMENDACION];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_medica_ambulancia','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))

$con->update($con->temporal.'.asistencia_medica_ambulancia',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");

else

$con->insert_reg($con->temporal.'.asistencia_medica_ambulancia',$reg);
//
//if ($_POST[IDLUGARORIGEN]!='')
//{
//	$asis_medica_asistencia_origen[IDASISTENCIA]=$_GET[idasistencia];
//	$con->update($con->temporal.'.asistencia_medica_ambulancia_origen',$asis_medica_asistencia_origen," WHERE ID ='$_POST[IDLUGARORIGEN]'");	
//}

if ($_POST[IDLUGARDESTINO]!='')
{
	$asis_medica_asistencia_destino[IDASISTENCIA]=$_GET[idasistencia];
	$con->update($con->temporal.'.asistencia_medica_ambulancia_destino',$asis_medica_asistencia_destino," WHERE ID ='$_POST[IDLUGARDESTINO]'");	
}


//print_r($_POST);

?>