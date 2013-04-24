<?
include_once('../../modelo/clase_mysqli.inc.php');
$idexpediente= $_POST[IDEXPEDIENTE];
$idasistencia= $_POST[IDASISTENCIA];
$arrprioridadatencion =$_POST[ARRPRIORIDADATENCION];


$con = new DB_mysqli();

/* DETERMINAR EL TIEMPO TRANSCURRIDO DESDE EL REGISTRO DEL EXPEDIENTE Y LA CONFIRMACION  Y COORDINADOR RESPONSABLE*/
$sql="
SELECT 
(
(SELECT UNIX_TIMESTAMP(FECHAMOD) FROM $con->temporal.asistencia_bitacora_etapa3  WHERE ARRCLASIFICACION ='CONF_SERV' AND idasistencia ='$idasistencia' ORDER BY FECHAMOD DESC LIMIT 1 )
-
(SELECT UNIX_TIMESTAMP(MAX(FECHAASIGNACION)) FROM  $con->temporal.asistencia_asig_proveedor WHERE IDASISTENCIA='$idasistencia')
) SEGUNDOS,
(SELECT IDUSUARIORESPONSABLE FROM $con->temporal.asistencia WHERE idasistencia= '$idasistencia') RESPONSABLE

FROM 
$con->temporal.asistencia a,
$con->temporal.expediente e
WHERE
e.IDEXPEDIENTE ='$idexpediente' 
AND a.IDEXPEDIENTE = e.IDEXPEDIENTE
";

$result = $con->query($sql);

while ($reg=$result->fetch_object())
	{
	$tiempo = intval($reg->SEGUNDOS/60)+(($reg->SEGUNDOS % 60)/100); //intval($reg->SEGUNDOS/60).'.'.($reg->SEGUNDOS % 60);
	$responsable = $reg->RESPONSABLE;
}

//echo $tiempo;
/* DETERMINA LA DEFICIENCIA */
if ($tiempo >=2 && $tiempo<4) $deficiencia='CP6';
elseif ($tiempo>=4) $deficiencia='CA5';

/* SI EXISTE DEFICIENCIA  SE GRABA */
if ($deficiencia!='')
{
	$def[IDEXPEDIENTE]=$idexpediente;
	$def[CVEDEFICIENCIA]=$deficiencia;
	$def[IDASISTENCIA]=$idasistencia;
	$def[IDSUPERVISOR]=$responsable;  //QUE ES EL COORDINADOR
	$def[IDCOORDINADOR]=$responsable;  //QUE ES EL COORDINADOR
	$def[ORIGEN]='AUTOMATICA';
	$def[IDETAPA]=3;
	$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
}


?>