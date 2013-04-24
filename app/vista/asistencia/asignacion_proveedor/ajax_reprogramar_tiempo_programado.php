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
$idusuario    = $_POST[IDUSUARIOMOD];
$idproveedor  = $_POST[IDPROVEEDOR];
$prioridad    = $_POST[PRIORIDAD];
$teat         = $_POST[TEAT];
$team         = $_POST[TEAM];
$prioridad    = $_POST[PRIORIDAD];
$fechaactual  = date('Y-m-d H:i:s');


$con = new DB_mysqli();
$asis = new asistencia();
$asis->carga_datos($idasistencia);

/* Determina el proveedor activo*/
foreach ($asis->proveedores as $prov)
{
	if ($prov[statusproveedor]=='AC')
	{
		$proveedor_act= $prov[idproveedor];
		$xteat = $prov[teat];
		$xteam = $prov[team];
		$idasignacion=$prov[idasigprov];
	}
}

$monitoreo_afiliado_arribo_programado=$con->lee_parametro('TIEMPO_MONITOREO_AFILIADO_ARRIBO_PROGRAMADO');

$idexpediente=$asis->expediente->idexpediente;

//SI EL TEAT NO HA SIDO MODIFICADO
if($xteat==$teat)
{
	/*  CALCULA LA NUEVA HORA PARA EL TEAM Y LA HORA DE MONITOREO AL AFILIADO*/
	$sql="
	SELECT 
		SUBDATE('$team',INTERVAL 5 MINUTE) ARRIBO_PROV,
		ADDDATE(NOW(), INTERVAL 2 MINUTE ) MONITOREO_AFILIADO,
		NOW() FECHA_ACTUAL
		";
	$result=$con->query($sql);
	while ($reg=$result->fetch_object()){
		$fechaactual = $reg->FECHA_ACTUAL;
		$arribo_prov = $reg->ARRIBO_PROV;
		$monitoreo_afiliado = $reg->MONITOREO_AFILIADO;
	}


	/* actualiza la tabla asistencia_asig_proveedor con el nuevo TEAM*/
	$asig[TEAM]=$team;
	$con->update("$con->temporal.asistencia_asig_proveedor",$asig," WHERE IDASISTENCIA=$idasistencia AND STATUSPROVEEDOR='AC' AND IDPROVEEDOR='$proveedor_act'");

	//REGISTRAMOS DATOS DE LA REPROGRAMACION
	$asiglog[IDASISTENCIA]=$idasistencia;
	$asiglog[TEATASIG]=$xteat;
	$asiglog[TEAMASIG]=$xteam;
	$asiglog[IDASIGPROV]=$idasignacion;
	$asiglog[TEAT]=$teat;
	$asiglog[TEAM]=$team;
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
	$tarea[STATUSTAREA]='PENDIENTE';
	$tarea[IDUSUARIO]=$idusuario;
	$tarea[DISPLAY] = 0;
	$con->insert_reg("$con->temporal.monitor_tarea",$tarea);

	/* ENVIA AL MONITOR LA NUEVA TAREA DE ARRIBO DEL PROV*/
	$tarea[IDTAREA]='ARR_PROV';
	$tarea[FECHATAREA]=$arribo_prov;
	$tarea[IDEXPEDIENTE]=$idexpediente;
	$tarea[IDASISTENCIA]=$idasistencia;
	$tarea[STATUSTAREA]='PENDIENTE';
	$tarea[IDUSUARIO]=$idusuario;
	$tarea[DISPLAY] = 0;
	$con->insert_reg("$con->temporal.monitor_tarea",$tarea);


}
else
{

	/* Variables programables*/
	$min_max_confirmacion = 2; // CALCULO DEL TIEMPO DE CONFIRMACION
	$min_max_activar_monitoreo_previo = 120; // CALCULO DEL MONITOREO PREVIO
	$min_pre_primermonitoreo = 60;    // CALCULO DEL MONITOREO PREVIO
	$min_pre_contacto = 5;		   // CALCULO DEL ARRIBO


	$asig[TEAT]=$teat;
	$asig[TEAM]=$team;
	$con->update("$con->temporal.asistencia_asig_proveedor",$asig," WHERE IDASISTENCIA=$idasistencia AND STATUSPROVEEDOR='AC' AND IDPROVEEDOR='$proveedor_act'");

	//REGISTRAMOS DATOS DE LA REPROGRAMACION
	$asiglog[IDASISTENCIA]=$idasistencia;
	$asiglog[TEATASIG]=$xteat;
	$asiglog[TEAMASIG]=$xteam;
	$asiglog[IDASIGPROV]=$idasignacion;
	$asiglog[TEAT]=$teat;
	$asiglog[TEAM]=$team;
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
  		AND IDTAREA IN ('ARR_PROV','MON_PROV','CONF_SERV') 
  		AND STATUSTAREA IN ('PENDIENTE')
  		
	";
	$con->query($sql);

	/* CALCULO DE LOS TIEMPOS */
	$sql="
		SELECT 
			IF ( TIMESTAMPDIFF(MINUTE,NOW(),'$teat') >= $min_max_activar_monitoreo_previo,
 			SUBDATE('$teat', INTERVAL $min_pre_primermonitoreo MINUTE) ,'') MONITOREO_PREVIO,
			'$teat' PRIMERMONITOREO,
			ADDDATE('$teat', INTERVAL (TIMESTAMPDIFF(MINUTE,'$teat','$team')/2) MINUTE ) SEGUNDOMONITOREO,
			SUBDATE('$team', INTERVAL $min_pre_contacto  MINUTE) TERCERMONITOREO,
			'$team' TIEMPOCONTACTO,
			ADDDATE(NOW(), INTERVAL $min_max_confirmacion  MINUTE) TIEMPO_CONFIRMACION,
			NOW() FECHAACTUAL
 			";
	$result=$con->query($sql);
	if($reg=$result->fetch_object())
	{
		$tiempo_confirmacion = $reg->TIEMPO_CONFIRMACION;
		$tiempo_monitoreoprevio = $reg->MONITOREOPREVIO;
		$tiempo_primermonitoreo = $reg->PRIMERMONITOREO;
		$tiempo_segundomonitoreo = $reg->SEGUNDOMONITOREO;
		$tiempo_tercermonitoreo = $reg->TERCERMONITOREO;
		$tiempo_contacto = $reg->TIEMPOCONTACTO;
		$fechaactual = $reg->FECHAACTUAL;
	}

	
	/* MONITOREO AL AFILIADO */
	$rowtareaafil[IDTAREA]='MON_AFIL';
	$rowtareaafil[IDEXPEDIENTE]=$idexpediente;
	$rowtareaafil[IDASISTENCIA]=$idasistencia;
	$rowtareaafil[RECORDATORIO]=1;
	$rowtareaafil[NUMMON]=1;
	$rowtareaafil[STATUSTAREA]='PENDIENTE';
	$rowtareaafil[IDUSUARIO]=$idusuario;
	$rowtareaafil[DISPLAY] = 0;
	$rowtareaafil[FECHATAREA]=$tiempo_confirmacion;
	$con->insert_reg("$con->temporal.monitor_tarea",$rowtareaafil);

	/* MONITOREO PREVIO */
	$num_monitoreo=1;

	$rowtareaprov[IDTAREA]='MON_PROV';
	$rowtareaprov[IDEXPEDIENTE]=$idexpediente;
	$rowtareaprov[IDASISTENCIA]=$idasistencia;
	$rowtareaprov[RECORDATORIO]=1;
	$rowtareaprov[STATUSTAREA]='PENDIENTE';
	$rowtareaprov[DISPLAY] = 0;
	$rowtareaprov[IDUSUARIO]=$idusuario;

	if ($tiempo_monitoreoprevio!='')
	{
		$rowtareaprov[NUMMON]=$num_monitoreo++;
		$rowtareaprov[FECHATAREA]=$tiempo_monitoreoprevio;
		$con->insert_reg("$con->temporal.monitor_tarea",$rowtareaprov);
	}

	/* PRIMER MONITOREO */
	$rowtareaprov[NUMMON]=$num_monitoreo++;
	$rowtareaprov[FECHATAREA]=$tiempo_primermonitoreo;
	$con->insert_reg("$con->temporal.monitor_tarea",$rowtareaprov);


	/* SEGUNDO MONITOREO */
	$rowtareaprov[NUMMON]=$num_monitoreo++;
	$rowtareaprov[FECHATAREA]=$tiempo_segundomonitoreo;
	$con->insert_reg("$con->temporal.monitor_tarea",$rowtareaprov);

	/* TERCER MONITOREO */
	$rowtareaprov[IDTAREA]='ARR_PROV';
	$rowtareaprov[NUMMON]=$num_monitoreo++;
	$rowtareaprov[FECHATAREA]=$tiempo_tercermonitoreo;
	$con->insert_reg("$con->temporal.monitor_tarea",$rowtareaprov);
}


switch ($prioridad)
{
	case 'EME': $etiqueta = 'EMERGENCIA'; break;
	case 'PRO': $etiqueta = 'PROGRAMADO'; break;
}


$comentario="REPROGRAMACION DE TIEMPOS\nPRIORIDAD ATENCION : $etiqueta\nTEAT ANTERIOR : $asiglog[TEATASIG]\nTEAM ANTERIOR : $asiglog[TEAMASIG]\nTEAT NUEVO : $asiglog[TEAT]\nTEAM NUEVO : $asiglog[TEAM]\nFECHA REPROGRAMACION : $fechaactual";

$asis_bitacora[COMENTARIO]=$comentario;
$asis_bitacora[IDUSUARIOMOD]=$idusuario;
$asis_bitacora[IDPROVEEDOR]=$proveedor_act;
$asis_bitacora[ARRCLASIFICACION] = 'BIT';
$asis_bitacora[IDASISTENCIA]=$idasistencia;

$con->insert_reg("$con->temporal.asistencia_bitacora_etapa2",$asis_bitacora);

?>