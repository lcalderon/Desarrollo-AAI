<?
session_start();
include_once('../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();


$idjustificacion = $_POST[JUSTIFICACION];
$observacion = $_POST[OBSERVACION];
$idasistencia = $_POST[IDASISTENCIA];
$idproveedor = $_POST[IDPROVEEDOR];
$status = $_POST[STATUS];


/* GRABA LOS DATOS DE LA JUSTIFICACION */
$justificacion[IDASISTENCIAJUST]="";
$justificacion[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];
$justificacion[IDJUSTIFICACION]=$idjustificacion;
$justificacion[IDASISTENCIA]=$idasistencia;
$justificacion[MOTIVO]=$observacion;
$respuesta=$con->insert_reg("$con->temporal.asistencia_justificacion",$justificacion);

/* CANCELA LAS TAREAS PENDIENTES */


switch ($status){
	case 'SGTE':	$titulo='SELECCIONAR SIGUIENTE PROVEEDOR'; break;
	case 'MANUAL':  $titulo='BUSQUEDA DE PROVEEDOR MANUAL'; break;
	case 'REP':		$titulo='REPROGRAMACION DE TIEMPOS'; break;
	case 'REASIG':	$titulo='REASIGNACION DE  PROVEEDOR'; break;
	case 'CP':		$titulo='CANCELADO POSTERIOR'; break;
	case 'CM':		$titulo='CANCELADO AL MOMENTO'; break;
}

/* DETERMINA EL MOTIVO DE LA JUSTIFICACION */
$sql="
	SELECT 
		MOTIVO,IDDEFICIENCIA
	FROM 
		$con->catalogo.catalogo_justificacion 
	WHERE 
		IDJUSTIFICACION = '$idjustificacion'
		
		";

$result=$con->query($sql);
if($reg=$result->fetch_object()){
	$motivo=$reg->MOTIVO;
	$cvedeficiencia=$reg->IDDEFICIENCIA;
}
	


/* GRABA LA BITACORA */
$comentario="$titulo\nMOTIVO JUSTIFICACION : $motivo\n\n$observacion";
$asis_bitacora[IDASISTENCIA]=$idasistencia;
$asis_bitacora[COMENTARIO]=$comentario;
$asis_bitacora[IDUSUARIOMOD]=$_SESSION['user'];
$asis_bitacora[IDPROVEEDOR]=$idproveedor;
$asis_bitacora[ARRCLASIFICACION] = $_POST[ARRCLASIFICACION];

$con->insert_reg("$con->temporal.asistencia_bitacora_etapa2",$asis_bitacora);


/* DETERMINA EXPEDIENTE Y RESPONSABLE */
$sql="
SELECT 
	IDEXPEDIENTE,
	IDUSUARIORESPONSABLE 
FROM 
	$con->temporal.asistencia 
WHERE 
	IDASISTENCIA = '$idasistencia'
	";


$result =$con->query($sql);
if($reg=$result->fetch_object())
{
	$idexpediente = $reg->IDEXPEDIENTE;
	$idusuarioresp = $reg->IDUSUARIORESPONSABLE;
}


/* BUSCA EL ORIGEN DE LA DEFICIENCIA */
$sql="
	SELECT 
		ORIGEN 
	FROM 
		$con->catalogo.catalogo_deficiencia 
	WHERE 
		CVEDEFICIENCIA='$cvedeficiencia'
		";

$result=$con->query($sql);
while($reg=$result->fetch_object()) $origen=$reg->ORIGEN;

/* GRABA LA DEFICIENCIA */
if($cvedeficiencia!='')
{
	$def[IDEXPEDIENTE]=$idexpediente;
	$def[IDPROVEEDOR]=($origen=='EXTERNO')?$idproveedor:'';
	$def[CVEDEFICIENCIA]=$cvedeficiencia;
	$def[IDASISTENCIA]=$idasistencia;
	$def[IDSUPERVISOR]=$idusuarioresp;
	$def[IDCOORDINADOR]=($origen=='EXTERNO')?'':$idusuarioresp;
	$def[ORIGEN]='AUTOMATICA';
	$def[IDETAPA]=2;
	$con->insert_reg("$con->temporal.expediente_deficiencia",$def);
}

?>