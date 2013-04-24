<?
include_once('../../modelo/clase_mysqli.inc.php');
$idexpediente= $_POST[IDEXPEDIENTE];
$idasistencia= $_POST[IDASISTENCIA];
$arrprioridadatencion =$_POST[ARRPRIORIDADATENCION];


$con = new DB_mysqli();

/* obtener las tareas  y calcular su tiempo con su ejecucion*/

$sql="
SELECT 
(
(SELECT 
	UNIX_TIMESTAMP(FECHAMOD) 
 FROM 
 	$con->temporal.asistencia_bitacora_etapa4 
 WHERE 
 	IDASISTENCIA='$idasistencia' 
 	AND ARRCLASIFICACION='MON_PROV' 
 	ORDER BY IDBITACORA DESC LIMIT 1
)
-
(SELECT 
	UNIX_TIMESTAMP(FECHATAREA) 
  FROM 
  	$con->temporal.monitor_tarea 
  WHERE 
  	idasistencia='$idasistencia' 
  	AND IDTAREA ='MON_PROV' 
  	AND STATUSTAREA in ('PENDIENTE','INVISIBLE')
  	ORDER BY ID ASC LIMIT 1)
) SEGUNDOS,
(SELECT 
	IDUSUARIORESPONSABLE 
  FROM 
  	$con->temporal.asistencia 
  WHERE 
  	IDASISTENCIA='$idasistencia'
  ) RESPONSABLE
";
//echo $sql;
$result = $con->query($sql);
while ($reg=$result->fetch_object())
{
	$tiempo = intval($reg->SEGUNDOS/60)+(($reg->SEGUNDOS % 60)/100);//ntval($reg->SEGUNDOS/60).'.'.($reg->SEGUNDOS % 60);
	$responsable = $reg->RESPONSABLE;
}

if ($tiempo >=6 && $tiempo<11) $deficiencia='CP6';
elseif ($tiempo>=11) $deficiencia='CP5';

/* grabar las deficiencias */
if ($deficiencia!='')
{
	$def[IDEXPEDIENTE]=$idexpediente;
	$def[CVEDEFICIENCIA]=$deficiencia;
	$def[IDASISTENCIA]=$idasistencia;
	$def[IDSUPERVISOR]=$responsable;  //QUE ES EL COORDINADOR
	$def[IDCOORDINADOR]=$responsable;  //QUE ES EL COORDINADOR
	$def[ORIGEN]='AUTOMATICA';
	$def[IDETAPA]=4;
	$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
}

/*   CAMBIAR EL STATUS DE LA TAREA */

$sql="
update 
	$con->temporal.monitor_tarea 
set 
	STATUSTAREA='ATENDIDA' 
WHERE 
 	IDASISTENCIA='$idasistencia' 
  	AND STATUSTAREA IN ('PENDIENTE','INVISIBLE' )
  	AND IDTAREA ='MON_PROV'
  	ORDER BY ID ASC  LIMIT 1
	  	";

$con->query($sql);


?>