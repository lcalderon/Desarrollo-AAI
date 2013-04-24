<?
include_once('../../../../modelo/clase_mysqli.inc.php');


$con = new DB_mysqli();


$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[SINTOMATOLOGIA]=$_POST[SINTOMATOLOGIA];
$reg[ANTECEDENTESCLINICOS]=$_POST[ANTECEDENTESCLINICOS];
$reg[DIAGNOSTICO]=$_POST[DIAGNOSTICO];
$reg[RECOMENDACION]=$_POST[RECOMENDACION];
$reg[PRESCRIPCIONMEDICA]=$_POST[PRESCRIPCIONMEDICA];
$reg[ARRTIPOPRESTACION]=$_POST[ARRTIPOPRESTACION];
$reg[ARRTIPOATENCION]=$_POST[ARRTIPOATENCION];

$reg[MEDICOTRATANTE]=$_POST[MEDICOTRATANTE];

$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_medica_prestacionmedica','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
$con->update($con->temporal.'.asistencia_medica_prestacionmedica',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");
else
$con->insert_reg($con->temporal.'.asistencia_medica_prestacionmedica',$reg);



//print_r($_POST);

?>