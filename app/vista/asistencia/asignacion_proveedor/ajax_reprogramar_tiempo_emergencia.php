<?
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_ubigeo.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');
include_once('../../../modelo/clase_plantilla.inc.php');
include_once('../../../modelo/clase_persona.inc.php');
include_once('../../../modelo/clase_telefono.inc.php');
include_once('../../../modelo/clase_cuenta.inc.php');
include_once('../../../modelo/clase_familia.inc.php');
include_once('../../../modelo/clase_servicio.inc.php');
include_once('../../../modelo/clase_programa_servicio.inc.php');
include_once('../../../modelo/clase_programa.inc.php');
include_once('../../../modelo/clase_afiliado.inc.php');
include_once('../../../modelo/clase_etapa.inc.php');
include_once('../../../modelo/clase_contacto.inc.php');
include_once('../../../modelo/clase_poligono.inc.php');
include_once('../../../modelo/clase_circulo.inc.php');
include_once('../../../modelo/clase_proveedor.inc.php');
include_once('../../../modelo/clase_expediente.inc.php');
include_once('../../../modelo/clase_asistencia.inc.php');



/* datos pasados como parametros */
$idasistencia = $_POST[IDASISTENCIA];
$idusuario 	  = $_POST[IDUSUARIOMOD];
$idproveedor  = $_POST[IDPROVEEDOR];
$minutos 	  = $_POST[MINUTO];
$prioridad	  = $_POST[PRIORIDAD];


$con  = new DB_mysqli();
$asis = new asistencia();

$asis->carga_datos($idasistencia);


$monitoreo_afiliado_arribo_emergencia=$con->lee_parametro('TIEMPO_MONITOREO_AFILIADO_ARRIBO_EMERGENCIA');

/* Determina el proveedor*/
foreach ($asis->proveedores as $prov)
{
	if ($prov[statusproveedor]=='AC')
	{
		$proveedor_act= $prov[idproveedor];
		$teat = $prov[teat];
		$team = $prov[team];
		$idasignacion=$prov[idasigprov];
	}
}

$idexpediente=$asis->expediente->idexpediente;

/*  CALCULA LA NUEVA HORA PARA EL TEAM Y LA HORA DE MONITOREO AL AFILIADO*/
$sql="
	SELECT
		ADDDATE('$team', INTERVAL $minutos MINUTE) TEAM,
		ADDDATE('$team', INTERVAL ($minutos - 5) MINUTE) ARRIBO_PROV,
		ADDDATE(NOW(), INTERVAL 2 MINUTE ) MONITOREO_AFILIADO,
		NOW() FECHA_ACTUAL
	";
$result=$con->query($sql);

if($reg=$result->fetch_object()){
	$asig[TEAM] = $reg->TEAM;
	$arribo_prov = $reg->ARRIBO_PROV;
	$monitoreo_afiliado = $reg->MONITOREO_AFILIADO;
	$fechaactual = $reg->FECHA_ACTUAL;
}

/* ACTUALIZA LA TABLA asistencia_asig_proveedor con el nuevo TEAM*/
$con->update("$con->temporal.asistencia_asig_proveedor",$asig," WHERE IDASISTENCIA = $idasistencia AND STATUSPROVEEDOR='AC' AND IDPROVEEDOR=$proveedor_act");



$asiglog[IDASISTENCIA]=$idasistencia;
$asiglog[TEATASIG]=$teat;
$asiglog[TEAMASIG]=$team;
$asiglog[IDASIGPROV]=$idasignacion;
$asiglog[TEAT]=$teat;
$asiglog[TEAM]=$asig[TEAM];
$asiglog[IDUSUARIOMOD]=$idusuario;
$asiglog[MOTIVO]='REPROGRAMACION';
$asiglog[ARRPRIORIDADATENCION]=$prioridad;
$con->insert_reg("$con->temporal.asistencia_asignacion_reprogramado",$asiglog);

/* CAMBIA EL STATUS A CANCELADO LAS TAREAS QUE ESTAN POR CUMPLIRSE */
$sql="
UPDATE 
   $con->temporal.monitor_tarea
SET
   STATUSTAREA='CANCELADA'
WHERE
   IDASISTENCIA='$idasistencia' 
  AND IDTAREA IN ('ARR_PROV','MON_AFIL','CONF_SERV') 
  AND STATUSTAREA IN ('PENDIENTE','INVISIBLE')
 
  
";

$con->query($sql);

/* ENVIA AL MONITOR LA TAREA DE MONITOREO AL AFILIADO */
$tarea[IDTAREA]='MON_AFIL';
$tarea[FECHATAREA]=$monitoreo_afiliado;
$tarea[IDEXPEDIENTE]=$idexpediente;
$tarea[IDASISTENCIA]=$idasistencia;
$tarea[RECORDATORIO]=1;
$tarea[DISPLAY]=0;
$tarea[STATUSTAREA]='PENDIENTE';
$tarea[IDUSUARIO]=$idusuario;
$con->insert_reg("$con->temporal.monitor_tarea",$tarea);


/* ENVIA AL MONITOR LA NUEVA TAREA DE ARRIBO DEL PROV*/
$tarea[IDTAREA]='ARR_PROV';
$tarea[FECHATAREA]=$arribo_prov;
$tarea[IDEXPEDIENTE]=$idexpediente;
$tarea[IDASISTENCIA]=$idasistencia;
$tarea[RECORDATORIO]=1;
$tarea[DISPLAY]=0;
$tarea[STATUSTAREA]='PENDIENTE';
$tarea[IDUSUARIO]=$idusuario;
$con->insert_reg("$con->temporal.monitor_tarea",$tarea);

switch ($prioridad)
{
	case 'EME': $etiqueta = 'EMERGENCIA'; break;
	case 'PRO': $etiqueta = 'PROGRAMADO'; break;
}


$comentario="REPROGRAMACION DE TIEMPOS\nPRIORIDAD ATENCION : $etiqueta\nTEAT ANTERIOR : $asiglog[TEATASIG]\nTEAM ANTERIOR : $asiglog[TEAMASIG]\nTEAT NUEVO : $asiglog[TEAT]\nTEAM NUEVO : $asiglog[TEAM]\nFECHA REPROGRAMACION : $fechaactual";
$asis_bitacora[IDASISTENCIA]=$idasistencia;
$asis_bitacora[COMENTARIO]=$comentario;
$asis_bitacora[IDUSUARIOMOD]=$idusuario;
$asis_bitacora[IDPROVEEDOR]=$proveedor_act;
$asis_bitacora[ARRCLASIFICACION] ='BIT';

$con->insert_reg("$con->temporal.asistencia_bitacora_etapa2",$asis_bitacora);

?>