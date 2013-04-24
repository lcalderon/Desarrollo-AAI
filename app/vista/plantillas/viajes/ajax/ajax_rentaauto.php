<?
include_once('../../../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();


$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[ARRTIPOMOVILIDAD] = $_POST[ARRTIPOMOVILIDAD];
$reg[NDIAS] = $_POST[NDIAS];
$reg[OBSERVACION] = $_POST[OBSERVACION];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_viajes_rentaauto','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
$con->update($con->temporal.'.asistencia_viajes_rentaauto',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");
else
$con->insert_reg($con->temporal.'.asistencia_viajes_rentaauto',$reg);


//print_r($reg);
?>