<?
include_once('../../../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();


$reg[IDASISTENCIA]=$_GET[idasistencia];
$reg[FECHAORIGEN] = $_POST[FECHAORIGEN].":".$_POST[dcbhora1].":".$_POST[dcbminuto1];
$reg[FECHADESTINO] = $_POST[FECHADESTINO].":".$_POST[dcbhora1dest].":".$_POST[dcbminuto1dest];

$reg[LUGARORIGEN] = $_POST[LUGARORIGEN];
$reg[LUGARDESTINO] = $_POST[LUGARDESTINO];
$reg[OBSERVACION] = $_POST[OBSERVACION];
$reg[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

if ($con->exist($con->temporal.'.asistencia_viajes_nacionalinternacional','IDASISTENCIA'," where IDASISTENCIA = '$_GET[idasistencia]'"))
$con->update($con->temporal.'.asistencia_viajes_nacionalinternacional',$reg," WHERE IDASISTENCIA ='$_GET[idasistencia]'");
else
$con->insert_reg($con->temporal.'.asistencia_viajes_nacionalinternacional',$reg);

?>