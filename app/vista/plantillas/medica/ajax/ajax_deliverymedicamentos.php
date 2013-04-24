<?
include_once('../../../../modelo/clase_mysqli.inc.php');


$con = new DB_mysqli();


$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[NOMBREMEDICAMENTO] = $_POST[NOMBREMEDICAMENTO];
$reg[IDLUGARENTREGA] = $_POST[IDLUGARENTREGA];
$reg[NOMBREDESTINATARIO]=$_POST[NOMBREDESTINATARIO];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_medica_deliverymedicamentos','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
$con->update($con->temporal.'.asistencia_medica_deliverymedicamentos',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");
else
$con->insert_reg($con->temporal.'.asistencia_medica_deliverymedicamentos',$reg);



if ($_POST[IDLUGARENTREGA]!='')
{
	$asis_medica_asistencia_origen[IDASISTENCIA]=$_GET[idasistencia];
	$con->update($con->temporal.'.asistencia_medica_deliverymedicamentos_ubigeo',$asis_medica_asistencia_origen," WHERE ID ='$_POST[IDLUGARORIGEN]'");	
}





print_r($_POST);

?>