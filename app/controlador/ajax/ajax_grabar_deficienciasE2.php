<?
include_once('../../modelo/clase_mysqli.inc.php');
$idexpediente= $_POST[IDEXPEDIENTE];
$idasistencia= $_POST[IDASISTENCIA];
$arrprioridadatencion =$_POST[ARRPRIORIDADATENCION];


$con = new DB_mysqli();

$sql="
SELECT 
(
(SELECT UNIX_TIMESTAMP(FECHAMOD) FROM $con->temporal.asistencia_bitacora_etapa2  WHERE ARRCLASIFICACION ='ASIG' AND IDASISTENCIA ='$idasistencia' ORDER BY FECHAMOD DESC LIMIT 1 ) -
(IF (MIN(a.IDASISTENCIA)='$idasistencia',
	 UNIX_TIMESTAMP((SELECT FECHAHORA FROM $con->temporal.expediente_usuario WHERE IDEXPEDIENTE='$idexpediente' AND ARRTIPOMOVEXP='APE')), 
	 UNIX_TIMESTAMP((SELECT MIN(fechahora) FROM $con->temporal.asistencia_usuario WHERE IDASISTENCIA ='$idasistencia' AND IDETAPA=1))
)))SEGUNDOS,
(SELECT IDUSUARIORESPONSABLE FROM $con->temporal.asistencia WHERE IDASISTENCIA= '$idasistencia') RESPONSABLE,
IF ((SELECT COUNT(*) FROM $con->temporal.asistencia_bitacora_etapa2 WHERE IDASISTENCIA ='$idasistencia' AND ARRCLASIFICACION='DEM_ASIG'),1,0) MONITOREO_DEMORA,
IF ((

SELECT COUNT(*) 
FROM $con->temporal.asistencia_bitacora_etapa2 ab2
LEFT JOIN $con->temporal.monitor_tarea mt ON mt.IDASISTENCIA ='$idasistencia' AND mt.IDTAREA ='ASIG_PROV'
WHERE 
ab2.IDASISTENCIA ='$idasistencia' 
AND ab2.FECHAMOD <= mt.FECHATAREA

),1,0) BITACORA
FROM 
$con->temporal.asistencia a,
$con->temporal.expediente e
WHERE
e.IDEXPEDIENTE ='$idexpediente' 
AND a.IDEXPEDIENTE = e.IDEXPEDIENTE
"
;


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

$sql="
SELECT 
(
(SELECT UNIX_TIMESTAMP(FECHAMOD) FROM $con->temporal.asistencia_bitacora_etapa2  WHERE ARRCLASIFICACION ='ASIG' AND IDASISTENCIA ='$idasistencia' ORDER BY FECHAMOD DESC LIMIT 1 ) -
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


$deficiencia='';
if ($tiempo>10 && !$monitoreo_demora) $deficiencia='CA5';


if ($deficiencia!='')
{
	$def[CVEDEFICIENCIA]=$deficiencia;
	$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
}

/* CUENTA LAS DEFICIENCIAS PP2,PP3,PP4 */
$sql="
SELECT 
COUNT(*) CONT_DEF
FROM 
$con->temporal.expediente_deficiencia 
WHERE 
IDASISTENCIA='$idasistencia' 
AND IDETAPA=2
AND CVEDEFICIENCIA IN ('PP2','PP3','PP4')
";
$result = $con->query($sql);
while ($reg = $result->fetch_object()) $cont_def = $reg->CONT_DEF;


if ($cont_def>=2)
{
	$def[IDEXPEDIENTE]=$idexpediente;
	$def[CVEDEFICIENCIA]='PA4';
	$def[IDASISTENCIA]=$idasistencia;
	$def[IDCOORDINADOR]=$responsable;
	$def[ORIGEN]='AUTOMATICA';
	$def[IDETAPA]=2;
	$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
}


?>