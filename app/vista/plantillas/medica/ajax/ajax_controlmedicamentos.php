<?
include_once('../../../../modelo/clase_mysqli.inc.php');


$con = new DB_mysqli();


$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[NOMBREMEDICAMENTO] = $_POST[NOMBREMEDICAMENTO];
$reg[PRESCRIPCIONMEDICA] = $_POST[PRESCRIPCIONMEDICA];
$reg[PROGRAMACION]=$_POST[PROGRAMACION];
$reg[HORAPRIMERATOMA]=$_POST[HORAPRIMERATOMA];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_medica_controlmedicamentos','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
$con->update($con->temporal.'.asistencia_medica_controlmedicamentos',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");
else
$con->insert_reg($con->temporal.'.asistencia_medica_controlmedicamentos',$reg);


print_r($_POST);

?>