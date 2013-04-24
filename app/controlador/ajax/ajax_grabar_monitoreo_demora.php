<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();

$asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis_bitacora[COMENTARIO]=$_POST[COMENTARIO];
$asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];
$asis_bitacora[ARRCLASIFICACION]=$_POST[ARRCLASIFICACION];

$con->insert_reg("$con->temporal.asistencia_bitacora_etapa2",$asis_bitacora);

/* CAMBIA STATUS DE LA TAREA */

$sql="
UPDATE
 $con->temporal.monitor_tarea
SET
	STATUSTAREA='ATENDIDA'
WHERE
  IDASISTENCIA ='$_POST[IDASISTENCIA]'	
  AND STATUSTAREA IN ('PENDIENTE','INVISIBLE')
  AND IDTAREA ='DEM_ASIG'
";

$con->query($sql);


?>
