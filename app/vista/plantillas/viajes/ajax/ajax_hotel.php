<?
include_once('../../../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();


$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[NOMBREHOTEL] = $_POST[NOMBREHOTEL];
$reg[INICIOHOSPEDAJE] = $_POST[INICIOHOSPEDAJE];
$reg[FINHOSPEDAJE] = $_POST[FINHOSPEDAJE];
$reg[NDIAS] = $_POST[NDIAS];
$reg[NPERSONAS] = $_POST[NPERSONAS];
$reg[DIRECCIONHOTEL] = $_POST[DIRECCIONHOTEL];
$reg[RECOMENDACION] = $_POST[RECOMENDACION];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_viajes_hotel','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
$con->update($con->temporal.'.asistencia_viajes_hotel',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");
else
$con->insert_reg($con->temporal.'.asistencia_viajes_hotel',$reg);

?>