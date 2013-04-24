<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();


/* graba la tarea de asignacion 5 minutos despues */

$monitor[IDUSUARIO] = $_POST[IDUSUARIO];
$monitor[IDEXPEDIENTE] = $_POST[IDEXPEDIENTE];
$monitor[IDASISTENCIA] = $_POST[IDASISTENCIA];


$sql="
SELECT
(IF (MIN(a.IDASISTENCIA)='$_POST[IDASISTENCIA]',
	 ADDDATE((SELECT FECHAHORA FROM $con->temporal.expediente_usuario WHERE IDEXPEDIENTE='$_POST[IDEXPEDIENTE]' AND ARRTIPOMOVEXP='REG'), INTERVAL 5 MINUTE), 
	 ADDDATE((SELECT MIN(fechahora) FROM $con->temporal.asistencia_usuario WHERE IDASISTENCIA ='$_POST[IDASISTENCIA]' AND IDETAPA=1), INTERVAL 5 MINUTE)
))HORATAREA
FROM 
$con->temporal.asistencia a,
$con->temporal.expediente e
WHERE
e.IDEXPEDIENTE ='$_POST[IDEXPEDIENTE]' 
AND a.IDEXPEDIENTE = e.IDEXPEDIENTE;

";
$result= $con->query($sql);
while($reg=$result->fetch_object())
{
	$horatarea= $reg->HORATAREA;
	$monitor[IDTAREA] = 'ASIG_PROV';
	$monitor[FECHATAREA] = $horatarea;
	$monitor[STATUSTAREA] = 'PENDIENTE';
	$con->insert_reg("$con->temporal.monitor_tarea",$monitor);
}

/* Lanza la tarea de llamada por demora de asignacion despues de 10 minutos */

$sql="
SELECT
(IF (MIN(a.IDASISTENCIA)='$_POST[IDASISTENCIA]',
	 ADDDATE((SELECT FECHAHORA FROM $con->temporal.expediente_usuario WHERE IDEXPEDIENTE='$_POST[IDEXPEDIENTE]' AND ARRTIPOMOVEXP='APE'), INTERVAL 10 MINUTE), 
	 ADDDATE((SELECT MIN(fechahora) FROM $con->temporal.asistencia_usuario WHERE IDASISTENCIA ='$_POST[IDASISTENCIA]' AND IDETAPA=1), INTERVAL 10 MINUTE)
))HORATAREA
FROM 
$con->temporal.asistencia a,
$con->temporal.expediente e
WHERE
e.IDEXPEDIENTE ='$_POST[IDEXPEDIENTE]' 
AND a.IDEXPEDIENTE = e.IDEXPEDIENTE;
";

$result= $con->query($sql);
while($reg=$result->fetch_object())
{
	$horatarea= $reg->HORATAREA;
	$monitor[IDTAREA] = 'DEM_ASIG';
	$monitor[FECHATAREA] = $horatarea;
	$con->insert_reg("$con->temporal.monitor_tarea",$monitor);
}
return;
?>
