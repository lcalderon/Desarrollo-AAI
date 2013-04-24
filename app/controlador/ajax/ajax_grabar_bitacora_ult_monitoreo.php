<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();

$asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis_bitacora[ARRCLASIFICACION]=$_POST[ARRCLASIFICACION];
$asis_bitacora[COMENTARIO]=$_POST[COMENTARIO];
$asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];
$asis_bitacora[STATUSARRCON]=$_GET[modo];
$asis_bitacora[IDPROVEEDOR]=$_GET[proveedor_act];
$asis_bitacora[IDASIGPROV]=$_GET[idasigprov];

/* GRABA LA BITACORA*/
$con->insert_reg("$con->temporal.asistencia_bitacora_etapa6",$asis_bitacora);







/* CREA LA NUEVA TAREA DE ARRIBO*/
/* CALCULA LA FECHATAREA ARR_PROV 15 MINUTOS TEAM */
$sql="
SELECT
	ADDDATE(aap.TEAM, INTERVAL 15 MINUTE) FECHATAREA,
	a.IDEXPEDIENTE
FROM 
    $con->temporal.asistencia_asig_proveedor aap,
    $con->temporal.asistencia a
WHERE	
   aap.IDASISTENCIA = a.IDASISTENCIA
   AND  aap.IDASIGPROV ='$_GET[idasigprov]'
    
";


$result=$con->query($sql);
while ($reg=$result->fetch_object()) {
	$fechatarea=$reg->FECHATAREA;
	$idexpediente = $reg->IDEXPEDIENTE;
}



$tarea[IDTAREA]='ARR_PROV';
$tarea[IDEXPEDIENTE]=$idexpediente;
$tarea[IDASISTENCIA]=$_POST[IDASISTENCIA];
$tarea[RECORDATORIO]=1;
$tarea[NUMMON]=1;
$tarea[STATUSTAREA]='PENDIENTE';
$tarea[DISPLAY] = 0;
$tarea[IDUSUARIO]=$_POST[IDUSUARIOMOD];
$tarea[FECHATAREA]=$fechatarea;
$con->insert_reg("$con->temporal.monitor_tarea",$tarea);



?>