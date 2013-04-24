<?

include_once('../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

//$con->select_db($con->temporal);

$idasistencia = $_POST[IDASISTENCIA];
$idjustificacion = $_POST[JUSTIFICACION];
$arrprioridadatencion ='EME'; // SE TOMA LA PRIORIDAD POR DEFECTO = EME, porque hasta ese momento no hay asignacion

/* DETERMINA EXPEDIENTE Y RESPONSABLE*/
$sql="
SELECT 
	IDEXPEDIENTE,
	IDUSUARIORESPONSABLE 
FROM 
	$con->temporal.asistencia 
WHERE 
	IDASISTENCIA = '$idasistencia'
";

$result = $con->query($sql);
if($reg=$result->fetch_object())
{
	$idexpediente=$reg->IDEXPEDIENTE;
	$responsable = $reg->IDUSUARIORESPONSABLE;
}


$sql="
SELECT 
	IDDEFICIENCIA 
FROM 
	$con->catalogo.catalogo_justificacion 
WHERE 
IDJUSTIFICACION='$idjustificacion' 
AND ARRJUSTIFICACIONMODULO ='CM'
";
$result=$con->query($sql);
while($reg=$result->fetch_object()) $deficiencia=$reg->IDDEFICIENCIA;


/* COLOCA LA DEFICIENCIA */
if ($deficiencia!='')
{
	$def[IDEXPEDIENTE]=$idexpediente;
	$def[CVEDEFICIENCIA]=$deficiencia;
	$def[IDASISTENCIA]=$idasistencia;
	$def[IDCOORDINADOR]= $responsable;
	//	$def[IDPROVEEDOR]=$idproveedor;
	$def[ORIGEN]='AUTOMATICA';
	$def[IDETAPA]='2';
	$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
}



/* CAMBIA DE STATUS A LA ASISTENCIA  */
$asis[IDASISTENCIA]=$_POST[IDASISTENCIA];
$asis[ARRSTATUSASISTENCIA]='CM';
$con->update("$con->temporal.asistencia",$asis," WHERE IDASISTENCIA='$asis[IDASISTENCIA]'");


/* CANCELA LA TAREAS PENDIENTE E INVISIBLES */

$sql="
UPDATE
 $con->temporal.monitor_tarea
SET
 STATUSTAREA ='CANCELADA'
WHERE
 IDASISTENCIA='$_POST[IDASISTENCIA]'
 AND STATUSTAREA in ('PENDIENTE','INVISIBLE')
";

$con->query($sql);


/*  calcular deficiencias   */
/* cuantos minutos pasaron desde el inicio de las asistencia hasta que hizo el CM*/
/* podria ser deficiencia por asignacion y demora de asignacion */


$sql="
SELECT 
(
( UNIX_TIMESTAMP(NOW())) 
-
(IF (MIN(a.IDASISTENCIA)='$idasistencia',
	 UNIX_TIMESTAMP((SELECT FECHAHORA FROM $con->temporal.expediente_usuario WHERE IDEXPEDIENTE='$idexpediente' AND ARRTIPOMOVEXP='APE')), 
	 UNIX_TIMESTAMP((SELECT MIN(fechahora) FROM $con->temporal.asistencia_usuario WHERE IDASISTENCIA ='$idasistencia' AND IDETAPA=1))
)))SEGUNDOS,
(SELECT IDUSUARIORESPONSABLE FROM $con->temporal.asistencia WHERE IDASISTENCIA= '$idasistencia') RESPONSABLE,
IF ((SELECT COUNT(*) FROM $con->temporal.asistencia_bitacora_etapa2 WHERE IDASISTENCIA ='$idasistencia' AND ARRCLASIFICACION!='ASIG'),1,0) BITACORA,
IF ((SELECT COUNT(*) FROM $con->temporal.asistencia_bitacora_etapa2 WHERE IDASISTENCIA ='$idasistencia' AND ARRCLASIFICACION='DEM_ASIG'),1,0) MONITOREO_DEMORA
FROM 
$con->temporal.asistencia a,
$con->temporal.expediente e
WHERE
e.IDEXPEDIENTE ='$idexpediente' 
AND a.IDEXPEDIENTE = e.IDEXPEDIENTE;
";

$result = $con->query($sql);
while ($reg=$result->fetch_object())
{
	$tiempo = intval($reg->SEGUNDOS/60)+(($reg->SEGUNDOS % 60)/100); //intval($reg->SEGUNDOS/60).'.'.($reg->SEGUNDOS % 60);
	$responsable = $reg->RESPONSABLE;
	$bitacora = $reg->BITACORA;
	$monitoreo_demora = $reg->MONITOREO_DEMORA;
}


/* DETERMINAR LA DEFICIENCIA POR ASIGNACION TARDIA */
$deficiencia='';
if ($arrprioridadatencion=='EME'){
	if ($tiempo>5 && !$bitacora) $deficiencia='CA7';  //CASO  EMERGENCIA 5 MIN
}
else {
	if ($tiempo>20 && !$bitacora) $deficiencia='CA7'; // CASO PROGRAMADO 20 MIN
}


$def[IDEXPEDIENTE]=$idexpediente;
$def[IDASISTENCIA]=$idasistencia;
$def[IDSUPERVISOR]=$responsable;
$def[IDCOORDINADOR]=$responsable;
$def[ORIGEN]='AUTOMATICA';
$def[IDETAPA]=2;

if ($deficiencia!='')
{
	$def[CVEDEFICIENCIA]=$deficiencia;
	$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
}



/* DETERMINA DEFICIENCIA POR MONITOREO NO REALIZADO AL PROVEEDOR */
$deficiencia='';
if ($tiempo>10 && !$monitoreo_demora) $deficiencia='CA5';


if ($deficiencia!='')
{
	$def[CVEDEFICIENCIA]=$deficiencia;
	$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
}





?>