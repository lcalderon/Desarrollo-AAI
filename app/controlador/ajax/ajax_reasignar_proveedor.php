<?
include_once('../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

$idasistencia    = $_POST[IDASISTENCIA];
$idjustificacion = $_POST[JUSTIFICACION];
$idproveedor     = $_POST[IDPROVEEDOR];

/* MODIFICA LA ETAPA EN LA ASIGNACION */
$rows[IDETAPA]=2;
//$rows[ARRPRIORIDADATENCION]='EME';
$con->update("$con->temporal.asistencia",$rows," WHERE IDASISTENCIA=".$idasistencia);

/* MODIFICA EL STATUS  A "CANCELADO" DE LA ASIGNACION DEL PROVEEDOR */
$rowasig_prov[STATUSPROVEEDOR]='CA';
$con->update("$con->temporal.asistencia_asig_proveedor",$rowasig_prov," WHERE IDASISTENCIA=".$idasistencia." AND STATUSPROVEEDOR ='AC'");


$sql="
	UPDATE 
   		$con->temporal.monitor_tarea
	SET
   		STATUSTAREA='CANCELADA'
	WHERE
   		IDASISTENCIA='$idasistencia' 
  		AND IDTAREA IN ('ARR_PROV','MON_PROV','CONF_SERV') 
		AND STATUSTAREA IN ('PENDIENTE')
  	 	";
$con->query($sql);


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
	$idexpediente  = $reg->IDEXPEDIENTE;
	$idusuarioresp = $reg->IDUSUARIORESPONSABLE;
}

/* DETERMINA LA IDDEFEICIENCIA */
$sql="
SELECT 
	IDDEFICIENCIA 
FROM 
	$con->catalogo.catalogo_justificacion 
WHERE 
IDJUSTIFICACION='$idjustificacion' 
AND ARRJUSTIFICACIONMODULO ='RASI'
";
$result=$con->query($sql);
while($reg=$result->fetch_object()) $deficiencia=$reg->IDDEFICIENCIA;


/* BUSCA E ORIGEN DE LA DEFICIENCIA */
$sql="
	SELECT 
		ORIGEN 
	FROM 
		$con->catalogo.catalogo_deficiencia 
	WHERE 
		CVEDEFICIENCIA='$deficiencia'";
$result=$con->query($sql);
while($reg=$result->fetch_object()) $origen=$reg->ORIGEN;


/* COLOCA LA DEFICIENCIA */
if ($deficiencia!='')
{
	$def[IDEXPEDIENTE]=$idexpediente;
	$def[CVEDEFICIENCIA]=$deficiencia;
	$def[IDASISTENCIA]=$idasistencia;
	$def[IDSUPERVISOR]=$idusuarioresp;
	$def[IDCOORDINADOR] = ($origen=='EXTERNO')?'':$idusuarioresp;
	$def[IDPROVEEDOR]  =  ($origen=='EXTERNO')?$idproveedor:'';
	$def[ORIGEN]='AUTOMATICA';
	$def[IDETAPA]=2;
	$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
}



?>