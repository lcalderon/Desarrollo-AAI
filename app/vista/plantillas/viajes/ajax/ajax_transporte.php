<?
include_once('../../../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();


$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[ARRTIPOTRANSPORTE] = $_POST[ARRTIPOTRANSPORTE];
$reg[LINEATRANSPORTE] = $_POST[LINEATRANSPORTE];
$reg[NPERSONAS] = $_POST[NPERSONAS];
$reg[NOMBRES] = $_POST[NOMBRES];
$reg[ORIGEN] = $_POST[ORIGEN];
$reg[DESTINO] = $_POST[DESTINO];
$reg[OBSERVACION] = $_POST[OBSERVACION];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_viajes_transporte','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
$con->update($con->temporal.'.asistencia_viajes_transporte',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");
else
$con->insert_reg($con->temporal.'.asistencia_viajes_transporte',$reg);


//print_r($reg);
?>