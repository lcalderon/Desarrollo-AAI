<?
include_once('../../../../modelo/clase_mysqli.inc.php');
include_once('../../../../modelo/clase_poligono.inc.php');

$con = new DB_mysqli();


$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[ARRTIPOPRESTACION] = $_POST[ARRTIPOPRESTACION];
$reg[SINTOMATOLOGIA] = $_POST[SINTOMATOLOGIA];
$reg[ANTECEDENTESCLINICOS] = $_POST[ANTECEDENTESCLINICOS];
$reg[DIAGNOSTICO] = $_POST[DIAGNOSTICO];
$reg[RECOMENDACION] = $_POST[RECOMENDACION];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_mascota_atencionveterinaria','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))

$con->update($con->temporal.'.asistencia_mascota_atencionveterinaria',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");

else

$con->insert_reg($con->temporal.'.asistencia_mascota_atencionveterinaria',$reg);




print_r($_POST);

?>