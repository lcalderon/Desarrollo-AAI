<?
include_once('../../modelo/clase_mysqli.inc.php');
$idexpediente= $_POST[IDEXPEDIENTE];
$idasistencia= $_POST[IDASISTENCIA];

$con = new DB_mysqli();

/* obtener las tareas  y calcular su tiempo con su ejecucion*/

$sql="
SELECT 
(
(SELECT UNIX_TIMESTAMP(FECHAMOD) FROM $con->temporal.asistencia_bitacora_etapa5 WHERE IDASISTENCIA='$idasistencia' AND ARRCLASIFICACION='MON_AFIL' ORDER BY IDBITACORA DESC LIMIT 1
)
-
(SELECT UNIX_TIMESTAMP(FECHATAREA) FROM $con->temporal.monitor_tarea WHERE idasistencia='$idasistencia' AND IDTAREA ='MON_AFIL' ORDER BY ID DESC LIMIT 1)
) SEGUNDOS,
(SELECT IDUSUARIORESPONSABLE FROM $con->temporal.asistencia WHERE IDASISTENCIA='$idasistencia') RESPONSABLE
";

$result = $con->query($sql);
while ($reg=$result->fetch_object())
{
	$tiempo = intval($reg->SEGUNDOS/60)+(($reg->SEGUNDOS % 60)/100); //intval($reg->SEGUNDOS/60).'.'.($reg->SEGUNDOS % 60);
	$responsable = $reg->RESPONSABLE;
}


/* DETERMINAR LA DEFICIENCIA */
if ($tiempo >=6 && $tiempo<11) $deficiencia='CP6';
		elseif ($tiempo>=11) $deficiencia='CA5';
		




if ($deficiencia!='')
{
	$def[IDEXPEDIENTE]=$idexpediente;
	$def[CVEDEFICIENCIA]=$deficiencia;
	$def[IDASISTENCIA]=$idasistencia;
	$def[IDSUPERVISOR]=$responsable;  //QUE ES EL COORDINADOR
	$def[IDCOORDINADOR]=$responsable;  //QUE ES EL COORDINADOR
	$def[ORIGEN]='AUTOMATICA';
	$def[IDETAPA]=5;
	$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
}

/* grabar las deficiencias */
?>