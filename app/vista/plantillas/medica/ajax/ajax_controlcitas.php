<?
include_once('../../../../modelo/clase_mysqli.inc.php');


$con = new DB_mysqli();


$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[SINTOMATOLOGIA]=$_POST[SINTOMATOLOGIA];
$reg[ANTECEDENTESCLINICOS]=$_POST[ANTECEDENTESCLINICOS];
$reg[DIAGNOSTICO]=$_POST[DIAGNOSTICO];
$reg[RECOMENDACION]=$_POST[RECOMENDACION];
$reg[IDLUGARCITA]=$_POST[IDLUGARCITA];
$reg[FECHACITA]=$_POST[FECHACITA]." ".$_POST[CBHORACITA].':'.$_POST[CBMINUTOCITA].':00';
$reg[PROXIMACITA]=$_POST[PROXIMACITA]." ".$_POST[CBHORAPROXIMACITA].':'.$_POST[CBMINUTOPROXIMACITA].':00';
$reg[IDESPECIALIDAD]= $_POST[IDESPECIALIDAD];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_medica_controlcitas','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
$con->update($con->temporal.'.asistencia_medica_controlcitas',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");
else
$con->insert_reg($con->temporal.'.asistencia_medica_controlcitas',$reg);

if ($_POST[IDLUGARCITA]!='')
{
	$asis_medica_controlcitas_ubigeo[IDASISTENCIA]=$_GET[idasistencia];
	$con->update($con->temporal.'.asistencia_medica_controlcitas_ubigeo',$asis_medica_controlcitas_ubigeo," WHERE ID ='$_POST[IDLUGARCITA]'");	
}


print_r($_POST);

?>