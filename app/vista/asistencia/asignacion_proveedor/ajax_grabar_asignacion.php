 <?
include_once('../../../modelo/clase_mysqli.inc.php');

/* conexion con la BD*/
$con = new DB_mysqli();


/* datos que vienen por el POST */

$idasistencia = $_POST[IDASISTENCIA];
$idexpediente = $_POST[IDEXPEDIENTE];
$idusuario = $_POST[IDUSUARIOMOD];
$idproveedor = $_POST[IDPROVEEDOR];

$minutomin = $_POST[MINUTOMIN];
$minutomax = $_POST[MINUTOMAX];
$prioridad = $_POST[PRIORIDAD];
$idservicio = $_POST[IDSERVICIO];
$localforaneo = $_POST[LOCALFORANEO];
$nombre = $_POST[NOMBRE];

$comentario = $_POST[COMENTARIO];
$bitacora=$_POST[COMENTARIOBITACORA];

/* Variables programables*/
$min_max_confirmacion = 2; // CALCULO DEL TIEMPO DE CONFIRMACION

$min_max_activar_monitoreo_previo = 120; // CALCULO DEL MONITOREO PREVIO
$min_pre_primermonitoreo = 60;    // CALCULO DEL MONITOREO PREVIO
$min_pre_contacto = 5;		   // CALCULO DEL ARRIBO

//CANCELAMOS SI EXISTE UN PROVEEDOR ACTIVO
$rowsu[STATUSPROVEEDOR] = "CA";
$update = $con->update("$con->temporal.asistencia_asig_proveedor",$rowsu," WHERE IDASISTENCIA='$idasistencia AND STATUSPROVEEDOR='AC'");

//STATUS DE TAREA ASIG_PROV
$tareaup[STATUSTAREA] = 'ATENDIDA';
$con->update("$con->temporal.monitor_tarea",$tareaup," WHERE IDASISTENCIA='$idasistencia' AND IDTAREA = 'ASIG_PROV' AND STATUSTAREA='PENDIENTE' ");

$sql="
	SELECT 
		FECHATAREA,
		IDTAREA,
		STATUSTAREA 
	FROM 
		$con->temporal.monitor_tarea 
	WHERE  
		IDASISTENCIA='$idasistencia' 
		AND STATUSTAREA IN ('PENDIENTE','NO ATENDIDA')
	";
		
$result=$con->query($sql);
while($reg=$result->fetch_object())
{
	if($fechaactual<$reg->FECHATAREA)
	{
		$tarea[STATUSTAREA]='CANCELADA';
		$con->update("$con->temporal.monitor_tarea",$tarea," WHERE IDASISTENCIA='$idasistencia' AND STATUSTAREA = '$reg->STATUSTAREA' AND IDTAREA ='$reg->IDTAREA'");
	}
}

$tarea2[DISPLAY]=0;
$con->update("$con->temporal.monitor_tarea",$tarea2," WHERE IDASISTENCIA='$idasistencia' AND STATUSTAREA IN ('NO ATENDIDA','ABANDONO','ATENDIDA CON RETRASO') ");

/* OBTIENE TIPO DE SERVICIO Y CARACTERISTICAS */
$sql="

SELECT 
	a.IDSERVICIO,
	cs.VALIDACIONTIEMPO,
	cs.CONCLUCIONTEMPRANA,
	cs.CONCLUCIONCONPROVEEDOR
FROM 
	$con->temporal.asistencia a,
	$con->catalogo.catalogo_servicio cs
WHERE 
a.IDSERVICIO = cs.IDSERVICIO
AND a.IDASISTENCIA ='$idasistencia' 
";
//echo $sql;
$result=$con->query($sql);
while ($reg=$result->fetch_object()){
	$validacioncontiempo = $reg->VALIDACIONCONTIEMPO;
	$concluciontemprana = $reg->CONCLUCIONTEMPRANA;
	$conclucionconproveedor = $reg->CONCLUCIONCONPROVEEDOR;

}


if ($concluciontemprana!=1)
{
	//  TIEMPO DE CONTACTO EN EMERGENCIA 
	$con_eme = $con->lee_parametro('TIEMPO_CONTACTO_EMERGENCIA');

	switch ($prioridad)
	{
		case 'EME':
			{
				// calcula los tiempos
				$sql="
			SELECT 
				ADDDATE(NOW(), INTERVAL $minutomin MINUTE) TEAT,
				ADDDATE(NOW(), INTERVAL $minutomax MINUTE) TEAM,
				ADDDATE(NOW(), INTERVAL $min_max_confirmacion  MINUTE) TIEMPO_CONFIRMACION,
				ADDDATE(NOW(), INTERVAL $minutomax/2 MINUTE) PRIMERMONITOREO,
				ADDDATE(NOW(), INTERVAL $con_eme  MINUTE) TIEMPO_CONTACTO,
				NOW() FECHAACTUAL
			";
				
				
				$result=$con->query($sql);
				if($reg=$result->fetch_object())
				{
					$teat = $reg->TEAT;
					$team = $reg->TEAM;
					$primermonitoreo = $reg->PRIMERMONITOREO;
					$fechaactual = $reg->FECHAACTUAL;
					$tiempo_confirmacion = $reg->TIEMPO_CONFIRMACION;
					$tiempo_contacto=$reg->TIEMPO_CONTACTO;
				}
				
				
				//ASIGNA PROVEEDOR
				$asig[IDPROVEEDOR]=$idproveedor;
				$asig[IDASISTENCIA]=$idasistencia;
				$asig[STATUSPROVEEDOR]='AC';
				$asig[IDUSUARIOMOD]=$idusuario;
				$asig[LOCALFORANEO]=$localforaneo;
				$asig[TEAT] = $teat;
				$asig[TEAM] = $team;
				$asig[FECHAASIGNACION]= $fechaactual;
				$asig[ARRPRIORIDADATENCION]=$prioridad;
				$con->insert_reg("$con->temporal.asistencia_asig_proveedor",$asig);
				$idasigprov = $con->reg_id();


				// CONFIRMACION DEL SERVICIO 
				$rowtareaafil[IDTAREA]='CONF_SERV';
				$rowtareaafil[IDEXPEDIENTE]=$idexpediente;
				$rowtareaafil[IDASISTENCIA]=$idasistencia;
				$rowtareaafil[RECORDATORIO]=1;
				$rowtareaafil[NUMMON]=1;
				$rowtareaafil[STATUSTAREA]='PENDIENTE';
				$rowtareaafil[IDUSUARIO]=$idusuario;
				$rowtareaafil[DISPLAY] = 0;
				$rowtareaafil[FECHATAREA]=$tiempo_confirmacion;
				$con->insert_reg("$con->temporal.monitor_tarea",$rowtareaafil);


				// PRIMER MONITOREO
				$rowtareaprov[IDTAREA]='MON_PROV';
				$rowtareaprov[IDEXPEDIENTE]=$idexpediente;
				$rowtareaprov[IDASISTENCIA]=$idasistencia;
				$rowtareaprov[RECORDATORIO]=1;
				$rowtareaprov[NUMMON]=1;
				$rowtareaprov[STATUSTAREA]='PENDIENTE';
				$rowtareaprov[DISPLAY] = 0;
				$rowtareaprov[IDUSUARIO]=$idusuario;
				$rowtareaprov[FECHATAREA]=$primermonitoreo;
				$con->insert_reg("$con->temporal.monitor_tarea",$rowtareaprov);


				// SEGUNDO MONITOREO 
				$rowtareaprov[IDTAREA]='ULT_MON';
				$rowtareaprov[NUMMON]=2;
				$rowtareaprov[FECHATAREA]=$teat;
				$con->insert_reg("$con->temporal.monitor_tarea",$rowtareaprov);
				
				break;
			}
		case 'PRO':
			{
				$teat = $_POST[TEAT];
				$team = $_POST[TEAM];

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
					$tiempo_monitoreoprevio = $reg->MONITOREO_PREVIO;
					$tiempo_primermonitoreo = $reg->PRIMERMONITOREO;
					$tiempo_segundomonitoreo = $reg->SEGUNDOMONITOREO;
					$tiempo_tercermonitoreo = $reg->TERCERMONITOREO;
					$tiempo_contacto = $reg->TIEMPOCONTACTO;
					$fechaactual = $reg->FECHAACTUAL;
				}

				//ASIGNA PROVEEDOR
				$asig[IDPROVEEDOR]=$idproveedor;
				$asig[IDASISTENCIA]=$idasistencia;
				$asig[STATUSPROVEEDOR]='AC';
				$asig[IDUSUARIOMOD]=$idusuario;
				$asig[LOCALFORANEO]=$localforaneo;
				$asig[TEAT] = $teat;
				$asig[TEAM] = $team;
				$asig[FECHAASIGNACION]= $fechaactual;
				$asig[ARRPRIORIDADATENCION]=$prioridad;
				$con->insert_reg("$con->temporal.asistencia_asig_proveedor",$asig);
				$idasigprov = $con->reg_id();

				// CONFIRMACION DEL SERVICIO 
				$rowtareaafil[IDTAREA]='CONF_SERV';
				$rowtareaafil[IDEXPEDIENTE]=$idexpediente;
				$rowtareaafil[IDASISTENCIA]=$idasistencia;
				$rowtareaafil[RECORDATORIO]=1;
				$rowtareaafil[NUMMON]=1;
				$rowtareaafil[STATUSTAREA]='PENDIENTE';
				$rowtareaafil[IDUSUARIO]=$idusuario;
				$rowtareaafil[DISPLAY] = 0;
				$rowtareaafil[FECHATAREA]=$tiempo_confirmacion;
				$con->insert_reg("$con->temporal.monitor_tarea",$rowtareaafil);

				// MONITOREO PREVIO //
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

				// PRIMER MONITOREO 
				$rowtareaprov[NUMMON]=$num_monitoreo++;
				$rowtareaprov[FECHATAREA]=$tiempo_primermonitoreo;
				$con->insert_reg("$con->temporal.monitor_tarea",$rowtareaprov);

				// SEGUNDO MONITOREO 
				$rowtareaprov[NUMMON]=$num_monitoreo++;
				$rowtareaprov[FECHATAREA]=$tiempo_segundomonitoreo;
				$con->insert_reg("$con->temporal.monitor_tarea",$rowtareaprov);

				// TERCER MONITOREO 
				$rowtareaprov[IDTAREA]='ULT_MON';
				$rowtareaprov[NUMMON]=$num_monitoreo++;
				$rowtareaprov[FECHATAREA]=$tiempo_tercermonitoreo;
				$con->insert_reg("$con->temporal.monitor_tarea",$rowtareaprov);
				break;
			}
	} //FIN DEL SWITCH

	//ACTUALIZAMOS LA ETAPA
	$rows[IDETAPA]=3;
	$rows[ARRPRIORIDADATENCION]=$prioridad;
	$con->update("$con->temporal.asistencia",$rows," WHERE IDASISTENCIA = '$idasistencia'");


}

else
{
	//  TIEMPO DE CONTACTO EN EMERGENCIA
	$con_eme = $con->lee_parametro('TIEMPO_CONTACTO_EMERGENCIA');
	
	// CALCULAMOS LOS TIEMPOS 
	$sql="
			SELECT 
				ADDDATE(NOW(), INTERVAL $minutomin MINUTE) TEAT,
				ADDDATE(NOW(), INTERVAL $minutomax MINUTE) TEAM,
				ADDDATE(NOW(), INTERVAL $min_max_confirmacion  MINUTE) TIEMPO_CONFIRMACION,
				ADDDATE(NOW(), INTERVAL $minutomax/2 MINUTE) PRIMERMONITOREO,
				ADDDATE(NOW(), INTERVAL $con_eme  MINUTE) TIEMPO_CONTACTO,
				NOW() FECHAACTUAL
			";
	echo $sql;
	$result=$con->query($sql);
	if($reg=$result->fetch_object())
	{
		$teat = $reg->TEAT;
		$team = $reg->TEAM;
		$primermonitoreo = $reg->PRIMERMONITOREO;
		$fechaactual = $reg->FECHAACTUAL;
		$tiempo_confirmacion = $reg->TIEMPO_CONFIRMACION;
		$tiempo_contacto=$reg->TIEMPO_CONTACTO;
	}

	//ASIGNA PROVEEDOR
	$asig[IDPROVEEDOR]=$idproveedor;
	$asig[IDASISTENCIA]=$idasistencia;
	$asig[STATUSPROVEEDOR]='AC';
	$asig[IDUSUARIOMOD]=$idusuario;
	$asig[LOCALFORANEO]=$localforaneo;
	$asig[TEAT] = $teat;
	$asig[TEAM] = $team;
	$asig[FECHAASIGNACION]= $fechaactual;
	$asig[ARRPRIORIDADATENCION]=$prioridad;
	$con->insert_reg("$con->temporal.asistencia_asig_proveedor",$asig);
	$idasigprov = $con->reg_id();


	//ACTUALIZAMOS LA ETAPA
	$rows[IDETAPA]=7;
	$rows[ARRPRIORIDADATENCION]=$prioridad;
	$con->update("$con->temporal.asistencia",$rows," WHERE IDASISTENCIA = '$idasistencia'");

}

//REGISTRAMOS MOVIMIENTO DE USUARIO
$asisuser[IDASISTENCIA]=$idasistencia;
$asisuser[IDUSUARIO]=$idusuario;
$asisuser[IDETAPA]=2;
$con->insert_reg("$con->temporal.asistencia_usuario",$asisuser);




// PARAMETROS DEL SISTEMA 

$etiqueta = ($prioridad=='EME')?'EMERGENCIA':'PROGRAMADO';
$asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
$comentario="ASIGNACION DE PROVEEDOR\nPRIORIDAD ATENCION : $etiqueta\nTEAT : $asig[TEAT]\nTEAM : $asig[TEAM]\nFECHA ASIGNACION : $fechaasignacion\n\n$bitacora";

$asis_bitacora[COMENTARIO]=$comentario;
$asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];
$asis_bitacora[IDPROVEEDOR]=$_POST[IDPROVEEDOR];
$asis_bitacora[ARRCLASIFICACION]=$_POST[ARRCLASIFICACION];
$asis_bitacora[IDASIGPROV]=$idasigprov;

$con->insert_reg("$con->temporal.asistencia_bitacora_etapa2",$asis_bitacora);


?>
