<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();

$asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis_bitacora[ARRCLASIFICACION]=$_POST[ARRCLASIFICACION];
$asis_bitacora[COMENTARIO]=$_POST[COMENTARIO];
$asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];

$asis_bitacora[IDPROVEEDOR]=$_GET[proveedor_act];
$asis_bitacora[IDASIGPROV]=$_GET[idasigprov];

/* GRABA LA BITACORA*/
$con->insert_reg("$con->temporal.asistencia_bitacora_etapa6",$asis_bitacora);




/* GRABA LA FECHA DE ARRIBO EN LA TABLA asistencia_asig_proveedor */
$sql=
"
UPDATE
	$con->temporal.asistencia_asig_proveedor
	
SET
	FECHAARRIBO = '$_POST[FECHAARRIBO]'
WHERE
	IDASIGPROV = '$_GET[idasigprov]'

";
$con->query($sql);


/* CAMBIA DE STATUS LA TAREA DE ARRIBO */
$sql="
UPDATE
	$con->temporal.monitor_tarea
SET
	STATUSTAREA = 'ATENDIDA'
WHERE
	IDTAREA='ARR_PROV'
	AND IDASISTENCIA = '$_POST[IDASISTENCIA]'
	AND STATUSTAREA = 'PENDIENTE'
";
$con->query($sql);



/* CALCULA LA FECHATAREA CONT_AFIL 5 MINUTOS DESPUES DEL ARRIBO */
$sql="
SELECT
	ADDDATE(NOW(), INTERVAL 5 MINUTE) FECHATAREA

";
$result=$con->query($sql);
while ($reg=$result->fetch_object()) $fechatarea=$reg->FECHATAREA;

/* CREA LA NUEVA TAREA */
$tarea[IDTAREA]='CONT_AFIL';
$tarea[IDEXPEDIENTE]=$_POST[IDEXPEDIENTE];
$tarea[IDASISTENCIA]=$_POST[IDASISTENCIA];
$tarea[RECORDATORIO]=1;
$tarea[NUMMON]=1;
$tarea[STATUSTAREA]='PENDIENTE';
$tarea[DISPLAY] = 0;
$tarea[IDUSUARIO]=$_POST[IDUSUARIOMOD];
$tarea[FECHATAREA]=$fechatarea;
$con->insert_reg("$con->temporal.monitor_tarea",$tarea);





return;
?>