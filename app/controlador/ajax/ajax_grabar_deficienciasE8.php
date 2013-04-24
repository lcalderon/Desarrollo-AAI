<?
include_once('../../modelo/clase_mysqli.inc.php');
$idexpediente= $_POST[IDEXPEDIENTE];
$idasistencia= $_POST[IDASISTENCIA];
$arrprioridadatencion =$_POST[ARRPRIORIDADATENCION];

$con = new DB_mysqli();

/* DETERMINA SI LA ASISTENCIA ES DE LA FAMILIA REFERENCIA */
$sql="SELECT IDFAMILIA FROM $con->temporal.asistencia WHERE IDASISTENCIA ='$idasistencia'";
$result= $con->query($sql);
while ($reg= $result->fetch_object()) $idfamilia=$reg->IDFAMILIA;


if ($idfamilia!=2)  // solo si son diferentes a REFERENCIAS
{
		/* obtener las tareas  y calcular su tiempo con su ejecucion*/
	$sql="

SELECT 
(
(SELECT UNIX_TIMESTAMP(FECHAMOD) FROM $con->temporal.asistencia_bitacora_etapa8 WHERE IDASISTENCIA='$idasistencia' AND ARRCLASIFICACION ='LLCNF' ORDER BY IDBITACORA DESC LIMIT 1)
-
(SELECT UNIX_TIMESTAMP(FECHAMOD) FROM $con->temporal.asistencia_bitacora_etapa7 WHERE IDASISTENCIA='$idasistencia' AND ARRCLASIFICACION='PROV_CONC' ORDER BY IDBITACORA DESC LIMIT 1)
) SEGUNDOS,
(SELECT IDUSUARIORESPONSABLE FROM $con->temporal.asistencia WHERE IDASISTENCIA='$idasistencia') RESPONSABLE

";

	$result = $con->query($sql);
	while ($reg=$result->fetch_object())
	{
		$tiempo = intval($reg->SEGUNDOS/60)+(($reg->SEGUNDOS % 60)/100); //intval($reg->SEGUNDOS/60).'.'.($reg->SEGUNDOS % 60);
		$responsable = $reg->RESPONSABLE;
	}


	/* DETERMINA LOS RANGOS DE TIEMPO PARA LAS DEFICIENCIAS DE LA LLAMADA*/

	$sql="
	SELECT 
		ENROJO 
	FROM 
		$con->catalogo.catalogo_tarea 
	WHERE 
		IDTAREA='LLAM_CON' 
	";
	$result = $con->query($sql);
	while ($reg=$result->fetch_object()){
		$enrojo = $reg->ENROJO;
	}


	if ($tiempo >=$enrojo && $tiempo<11) $deficiencia='CP6';
	elseif ($tiempo>=11) $deficiencia='CA5';


	/* grabar las deficiencias */
	if ($deficiencia!='')
	{
		$def[IDEXPEDIENTE]=$idexpediente;
		$def[CVEDEFICIENCIA]=$deficiencia;
		$def[IDASISTENCIA]=$idasistencia;
		$def[IDSUPERVISOR]=$responsable;  //QUE ES EL COORDINADOR
		$def[IDCOORDINADOR]=$responsable;  //QUE ES EL COORDINADOR
		$def[ORIGEN]='AUTOMATICA';
		$def[IDETAPA]=8;
		$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
	}
}

?>