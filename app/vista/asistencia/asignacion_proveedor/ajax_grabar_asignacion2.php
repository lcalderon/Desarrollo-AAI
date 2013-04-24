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

$con = new DB_mysqli();
$con->select_db($con->temporal);
$catalogo = $con->catalogo;

$asis = new asistencia();
$asis->carga_datos($_POST[IDASISTENCIA]);

$idexpediente= $asis->expediente->idexpediente;

$idasistencia = $_POST[IDASISTENCIA];
$idusuario = $_POST[IDUSUARIOMOD];
$idproveedor =$_POST[IDPROVEEDOR];
$fechaactual = date('Y-m-d H:i:s');

/*Segun nueva definicion*/
$minutomin = $_POST[MINUTOMIN];
$minutomax = $_POST[MINUTOMAX];


$prioridad = $_POST[PRIORIDAD];
$idservicio = $_POST[IDSERVICIO];
$localforaneo=$_POST[LOCALFORANEO];
$teat=$_POST[TEAT];
$team=$_POST[TEAM];
$nombre = $_POST[NOMBRE];
$comentario = $_POST[COMENTARIO];
$bitacora=$_POST[COMENTARIOBITACORA];


/* DATOS PARA LA ASIGNACION */
$asig[IDPROVEEDOR]=$idproveedor;
$asig[IDASISTENCIA]=$idasistencia;
$asig[STATUSPROVEEDOR]='AC';
$asig[IDUSUARIOMOD]=$idusuario;
$asig[LOCALFORANEO]=$localforaneo;

/* PARAMETROS DEL SISTEMA */
$con_eme=$con->lee_parametro('TIEMPO_CONTACTO_EMERGENCIA');
$con_pro=$con->lee_parametro('TIEMPO_CONTACTO_PROGRAMADO');
$monitoreo_afiliado_arribo_emergencia=$con->lee_parametro('TIEMPO_MONITOREO_AFILIADO_ARRIBO_EMERGENCIA');
$monitoreo_afiliado_arribo_programado=$con->lee_parametro('TIEMPO_MONITOREO_AFILIADO_ARRIBO_PROGRAMADO');

//CANCELAMOS SI EXISTE UN PROVEEDOR ACTIVO
$rowsu['STATUSPROVEEDOR'] = "CA";
$update=$con->update("asistencia_asig_proveedor",$rowsu,"WHERE IDASISTENCIA=".$idasistencia." AND STATUSPROVEEDOR='AC'");


if($prioridad=='EME')
{
	//CALCULO TEAT = FECHAACTUAL TEAM = TEAT + MINUTOS
	//SEGUN NUEVA DEFINICION TEAT=FECHAACTUAL +MINUTOMIN - TEAM = FECHAACTUAL + MINUTOMAX
	$sql_team="
	SELECT 
		ADDDATE('$fechaactual', INTERVAL $minutomin MINUTE) TEAT,
		ADDDATE('$fechaactual', INTERVAL $minutomax MINUTE) TEAM,
		ADDDATE('$fechaactual', INTERVAL $con_eme  MINUTE) TIEMPO_CONTACTO
		";
	$exec_team=$con->query($sql_team);
	if($rset_team=$exec_team->fetch_object())
	{
		$asig[TEAT] = $rset_team->TEAT;
		$asig[TEAM] = $rset_team->TEAM;
		$tiempo_contacto=$rset_team->TIEMPO_CONTACTO;
	}

	//ASIGNACION DE PROVEEDOR
	$asig[FECHAASIGNACION]=$fechaactual;
	$con->insert_reg('asistencia_asig_proveedor',$asig);
	$idasigprov = $con->reg_id();

	//REGISTRAMOS MOVIMIENTO DE USUARIO
	$asisuser[IDASISTENCIA]=$idasistencia;
	$asisuser[IDUSUARIO]=$idusuario;
	$asisuser[IDETAPA]=2;
	$con->insert_reg('asistencia_usuario',$asisuser);

	//ACTUALIZAMOS LA ETAPA
	$rows[IDETAPA]=3;
	$rows[ARRPRIORIDADATENCION]=$prioridad;
	$con->update("asistencia",$rows,"WHERE IDASISTENCIA=".$idasistencia);

	//STATUS DE TAREA ASIG_PROV
	$tareaup[STATUSTAREA] = 'ATENDIDA';
	$con->update("monitor_tarea",$tareaup," WHERE IDASISTENCIA=$idasistencia AND IDTAREA='ASIG_PROV' AND STATUSTAREA='PENDIENTE'");


	$sql_tarea="SELECT FECHATAREA,IDTAREA,STATUSTAREA FROM monitor_tarea WHERE  IDASISTENCIA=$idasistencia AND STATUSTAREA IN ('PENDIENTE','NO ATENDIDA')";
	$exec_tarea=$con->query($sql_tarea);
	while($rset_tarea=$exec_tarea->fetch_object()){
		$fechatarea=$rset_tarea->FECHATAREA;
		if($fechaactual<$fechatarea){
			$tarea[STATUSTAREA]='CANCELADA';
			$con->update("monitor_tarea",$tarea," WHERE IDASISTENCIA=".$idasistencia." AND STATUSTAREA = '$rset_tarea->STATUSTAREA' AND IDTAREA ='$rset_tarea->IDTAREA'");
		}
	}
	$tarea2[DISPLAY]=0;
	$con->update("$con->temporal.monitor_tarea",$tarea2," WHERE IDASISTENCIA=$idasistencia AND STATUSTAREA IN ('NO ATENDIDA','ABANDONO','ATENDIDA CON RETRASO') ");


	//OBTENEMOS EL NUMERO DE MONITOREOS DEPEDNDIENDO DEL SERVICIO
	$sql_servicio="SELECT NUMMONITOREO FROM $catalogo.catalogo_servicio WHERE IDSERVICIO = $idservicio";
	// echo $sql_servicio;
	$exec_servicio = $con->query($sql_servicio);
	if($rset_servicio=$exec_servicio->fetch_object())
	{
		$nummonitoreo =$rset_servicio->NUMMONITOREO;
	}

	//CALCULAMOS LOS TIEMPOS DE LOS MONITOREOS
	if($minutomin>=20){
		$sql_monitoreo_proveedor="SELECT ROUND(ROUND(IF(HOUR( TIMEDIFF('$asig[TEAT]','$fechaactual'))>0,
	      (HOUR(TIMEDIFF('$asig[TEAT]','$fechaactual'))*60)/1+MINUTE(TIMEDIFF('$asig[TEAT]','$fechaactual')),MINUTE(TIMEDIFF('$asig[TEAT]','$fechaactual'))),0)/$nummonitoreo,0) NUM";
		//echo $sql_monitoreo_proveedor;
		$exec_monitoreo_proveedor=$con->query($sql_monitoreo_proveedor);
		if($rset_monitoreo_proveedor=$exec_monitoreo_proveedor->fetch_object())
		{
			$minutomonitoreo=$rset_monitoreo_proveedor->NUM;
		}

		for($i=1;$i<$nummonitoreo;$i++){
			$sql_monitoreo="SELECT ADDDATE(FECHAASIGNACION, INTERVAL $minutomonitoreo*$i MINUTE) MONITOREO FROM asistencia_asig_proveedor
	      WHERE IDASISTENCIA = $idasistencia AND STATUSPROVEEDOR = 'AC'";

			$exec_monitoreo = $con->query($sql_monitoreo);
			if($rset_monitoreo=$exec_monitoreo->fetch_object()){

				$rowtareaprov[IDTAREA]='MON_PROV';
				$rowtareaprov[FECHATAREA]=$rset_monitoreo->MONITOREO;
				$rowtareaprov[IDEXPEDIENTE]=$idexpediente;
				$rowtareaprov[IDASISTENCIA]=$idasistencia;
				$rowtareaprov[RECORDATORIO]=1;
				$rowtareaprov[NUMMON]=$i;
				$rowtareaprov[STATUSTAREA]='PENDIENTE';
				$rowtareaprov[DISPLAY] = 0;
				$rowtareaprov[IDUSUARIO]=$idusuario;
				$con->insert_reg('monitor_tarea',$rowtareaprov);

			}


		}

	}



	if (!$asis->servicio->conclucionconproveedor){

		if ($asis->servicio->validaciontiempo){

			$sql_monitoreo_afiliado="SELECT ADDDATE(FECHAASIGNACION, INTERVAL $con_eme MINUTE) TAREA_CONTACTO_AFILIADO,
				ROUND(ROUND(IF(HOUR( TIMEDIFF('$asig[TEAT]','$asig[TEAM]'))>0,
	      (HOUR(TIMEDIFF('$asig[TEAT]','$asig[TEAM]'))*60)/1+MINUTE(TIMEDIFF('$asig[TEAT]','$asig[TEAM]')),MINUTE(TIMEDIFF('$asig[TEAT]','$asig[TEAM]'))),0)/2,0) MINUTO_ARRIBO
				FROM asistencia_asig_proveedor 
				WHERE IDASISTENCIA= $idasistencia AND STATUSPROVEEDOR = 'AC'";
			//			echo $sql_monitoreo_afiliado;
			$exec_monitoreo_afiliado = $con->query($sql_monitoreo_afiliado);
			if($rset_monitoreo_afiliado=$exec_monitoreo_afiliado->fetch_object())
			{
				$fecha_tarea_contacto_afiliado = $rset_monitoreo_afiliado->TAREA_CONTACTO_AFILIADO;
				$minuto_arribo = $rset_monitoreo_afiliado->MINUTO_ARRIBO;
			}



			$rowtarea4[IDTAREA]='CONF_SERV';
			$rowtarea4[FECHATAREA]=$fecha_tarea_contacto_afiliado;
			$rowtarea4[IDEXPEDIENTE]=$idexpediente;
			$rowtarea4[IDASISTENCIA]=$idasistencia;
			$rowtarea4[STATUSTAREA]='PENDIENTE';
			$rowtarea4[IDUSUARIO]=$idusuario;
			$rowtarea4[DISPLAY] = 0;
			$con->insert_reg('monitor_tarea',$rowtarea4);



			$sql_monitoreo_arribo="SELECT FECHAASIGNACION,ADDDATE(TEAT, INTERVAL $minuto_arribo MINUTE) TAREA_MONITOREO_ARRIBO,
					ADDDATE(ADDDATE(TEAT, INTERVAL $minuto_arribo MINUTE) , INTERVAL $monitoreo_afiliado_arribo_emergencia MINUTE) TAREA_MONITOREO_AFILIADO
					FROM asistencia_asig_proveedor
					WHERE IDASISTENCIA = $idasistencia AND STATUSPROVEEDOR = 'AC'";

			$exec_monitoreo_arribo = $con->query($sql_monitoreo_arribo);
			if($rset_monitoreo_arribo=$exec_monitoreo_arribo->fetch_object())
			{
				$rowtareaprov[IDTAREA]='ARR_PROV';
				$rowtareaprov[FECHATAREA]=$rset_monitoreo_arribo->TAREA_MONITOREO_ARRIBO;
				$rowtareaprov[IDEXPEDIENTE]=$idexpediente;
				$rowtareaprov[IDASISTENCIA]=$idasistencia;
				$rowtareaprov[RECORDATORIO]=0;
				$rowtareaprov[NUMMON]=$i;
				$rowtareaprov[STATUSTAREA]='PENDIENTE';
				$rowtareaprov[DISPLAY] = 0;
				$rowtareaprov[IDUSUARIO]=$idusuario;
				$con->insert_reg('monitor_tarea',$rowtareaprov);

				$rowtarea3[IDTAREA]='CONT_AFIL';
				$rowtarea3[FECHATAREA]=$rset_monitoreo_arribo->TAREA_MONITOREO_AFILIADO;
				$rowtarea3[IDEXPEDIENTE]=$idexpediente;
				$rowtarea3[IDASISTENCIA]=$idasistencia;
				$rowtarea3[RECORDATORIO]=0;
				$rowtarea3[STATUSTAREA]='PENDIENTE';
				$rowtarea3[DISPLAY] = 0;
				$rowtarea3[IDUSUARIO]=$idusuario;
				$con->insert_reg('monitor_tarea',$rowtarea3);
				$fechaasignacion=$rset_monitoreo_arribo->FECHAASIGNACION;
			}
		} /* fin del if de VALIDACION DE TIEMPOS*/
	}

}
elseif($prioridad=='PRO')
{

	//VERIFICAMOS SI EXISTE UN PROVEEDOR
	$sql_prov="SELECT * from asistencia_asig_proveedor WHERE IDASISTENCIA = $idasistencia AND IDPROVEEDOR ='0' AND STATUSPROVEEDOR='AC'";
	$exec_prov = $con->query($sql_prov);
	$nreg_prov= $exec_prov->num_rows;
	if($nreg_prov==0)
	{
		//ASIGNACION DE PROVEEDOR
		$asig[TEAT]=$teat;
		$asig[TEAM]=$team;
		$asig[FECHAASIGNACION]=$fechaactual;

		$con->insert_reg('asistencia_asig_proveedor',$asig);
		$idasigprov = $con->reg_id();

		//REGISTRAMOS MOVIMIENTO DE USUARIO
		$asisuser[IDASISTENCIA]=$idasistencia;
		$asisuser[IDUSUARIO]=$idusuario;
		$asisuser[IDETAPA]=2;
		$con->insert_reg('asistencia_usuario',$asisuser);

		//ACTUALIZAMOS LA ETAPA
		$rows[IDETAPA]=3;
		$rows[ARRPRIORIDADATENCION]=$prioridad;
		$con->update("asistencia",$rows,"WHERE IDASISTENCIA=".$idasistencia);

		//STATUS DE TAREA ASIG_PROV
		$tareaup[STATUSTAREA] = 'ATENDIDA';
		$con->update("monitor_tarea",$tareaup," WHERE IDASISTENCIA=$idasistencia AND IDTAREA='ASIG_PROV' AND STATUSTAREA='PENDIENTE'");


		$sql_tarea="SELECT FECHATAREA,IDTAREA,STATUSTAREA FROM monitor_tarea WHERE  IDASISTENCIA=$idasistencia AND STATUSTAREA IN ('PENDIENTE','NO ATENDIDA')";
		$exec_tarea=$con->query($sql_tarea);
		while($rset_tarea=$exec_tarea->fetch_object()){
			$fechatarea=$rset_tarea->FECHATAREA;
			if($fechaactual<$fechatarea){
				$tarea[STATUSTAREA]='CANCELADA';
				$con->update("monitor_tarea",$tarea," WHERE IDASISTENCIA=".$idasistencia." AND STATUSTAREA = '$rset_tarea->STATUSTAREA' AND IDTAREA ='$rset_tarea->IDTAREA'");
			}
		}
		$tarea2[DISPLAY]=0;
		$con->update("$con->temporal.monitor_tarea",$tarea2," WHERE IDASISTENCIA=$idasistencia AND STATUSTAREA IN ('NO ATENDIDA','ABANDONO','ATENDIDA CON RETRASO')");


		$monprog = $con->lee_parametro('MONITOREO_PROGRAMADO');
		$monitoreo_afiliado_programado = $con->lee_parametro('MONITOREO_AFILIADO_PROGRAMADO');
		$monitoreo_proveedor_previo=$con->lee_parametro('TIEMPO_MONITOREO_PROVEEDOR_PREVIO');
		//$monitoreo_afiliado_programado_arribo=$con->lee_parametro('TIEMPO_MONITOREO_AFILIADO_ARRIBO_PROGRAMADO);

		$sql_monitoreo_24="SELECT  FECHAASIGNACION,
				    ROUND(IF(HOUR( TIMEDIFF(TEAT,FECHAASIGNACION))>0,
				    (HOUR(TIMEDIFF(TEAT,FECHAASIGNACION))*60)/1+MINUTE(TIMEDIFF(TEAT,FECHAASIGNACION)),
				    MINUTE(TIMEDIFF(TEAT,FECHAASIGNACION))),0) NUMMONITOREO,
				    SUBDATE(TEAT, INTERVAL $monitoreo_proveedor_previo MINUTE) MONITOREO_PROVEEDOR,
				    ADDDATE(FECHAASIGNACION,INTERVAL $con_pro MINUTE) MONITOREO_DISPONIBILIDAD_AFILIADO,
				    ADDDATE(SUBDATE(TEAT, INTERVAL 60 MINUTE), INTERVAL $monitoreo_afiliado_programado MINUTE) MONITOREO_AFILIADO,
				    ROUND(ROUND(IF(HOUR( TIMEDIFF(TEAT,TEAM))>0,
				    (HOUR(TIMEDIFF(TEAT,TEAM))*60)/1+MINUTE(TIMEDIFF(TEAT,TEAM)),
				    MINUTE(TIMEDIFF(TEAT,TEAM))),0)/2,0) NUMARRIBO,
				    ADDDATE(TEAT, INTERVAL ROUND(ROUND(IF(HOUR( TIMEDIFF(TEAT,TEAM))>0,
				    (HOUR(TIMEDIFF(TEAT,TEAM))*60)/1+MINUTE(TIMEDIFF(TEAT,TEAM)),
				    MINUTE(TIMEDIFF(TEAT,TEAM))),0)/2,0) MINUTE ) MONITOREO_ARRIBO,
				    ADDDATE(ADDDATE(TEAT, INTERVAL ROUND(ROUND(IF(HOUR( TIMEDIFF(TEAT,TEAM))>0,
				    (HOUR(TIMEDIFF(TEAT,TEAM))*60)/1+MINUTE(TIMEDIFF(TEAT,TEAM)),
				    MINUTE(TIMEDIFF(TEAT,TEAM))),0)/2,0) MINUTE ), INTERVAL $monitoreo_afiliado_arribo_programado MINUTE) MONITOREO_ARRIBO_AFIL
				    FROM asistencia_asig_proveedor 
				    WHERE IDASISTENCIA=$idasistencia AND STATUSPROVEEDOR='AC'";
		echo $sql_monitoreo_24;
		$exec_monitoreo_24 = $con->query($sql_monitoreo_24);
		if($rset_monitoreo_24=$exec_monitoreo_24->fetch_object())
		{
			$nummonitoreo =$rset_monitoreo_24->NUMMONITOREO;


			$rowtarea1[IDTAREA]='CONF_SERV';
			$rowtarea1[FECHATAREA]=$rset_monitoreo_24->MONITOREO_DISPONIBILIDAD_AFILIADO;
			$rowtarea1[IDEXPEDIENTE]=$idexpediente;
			$rowtarea1[IDASISTENCIA]=$idasistencia;
			$rowtarea1[STATUSTAREA]='PENDIENTE';
			$rowtarea1[IDUSUARIO]=$idusuario;
			$rowtarea1[DISPLAY] = 0;
			$con->insert_reg('monitor_tarea',$rowtarea1);

			$rowtarea2[IDTAREA]='MON_PROV';
			$rowtarea2[FECHATAREA]=$rset_monitoreo_24->MONITOREO_PROVEEDOR;
			$rowtarea2[IDEXPEDIENTE]=$idexpediente;
			$rowtarea2[IDASISTENCIA]=$idasistencia;
			$rowtarea2[STATUSTAREA]='PENDIENTE';
			$rowtarea2[NUMMON]=2;
			$rowtarea2[IDUSUARIO]=$idusuario;
			$rowtarea2[DISPLAY] = 0;
			$con->insert_reg('monitor_tarea',$rowtarea2);

			$rowtarea3[IDTAREA]='MON_AFIL';
			$rowtarea3[FECHATAREA]=$rset_monitoreo_24->MONITOREO_AFILIADO;
			$rowtarea3[IDEXPEDIENTE]=$idexpediente;
			$rowtarea3[IDASISTENCIA]=$idasistencia;
			$rowtarea3[STATUSTAREA]='PENDIENTE';
			$rowtarea3[IDUSUARIO]=$idusuario;
			$rowtarea3[DISPLAY] = 0;
			$con->insert_reg('monitor_tarea',$rowtarea3);

			$rowtarea4[IDTAREA]='ARR_PROV';
			$rowtarea4[FECHATAREA]=$rset_monitoreo_24->MONITOREO_ARRIBO;
			$rowtarea4[IDEXPEDIENTE]=$idexpediente;
			$rowtarea4[IDASISTENCIA]=$idasistencia;
			$rowtarea4[STATUSTAREA]='PENDIENTE';
			$rowtarea4[IDUSUARIO]=$idusuario;
			$rowtarea4[DISPLAY] = 0;
			$con->insert_reg('monitor_tarea',$rowtarea4);

			$rowtarea5[IDTAREA]='CONT_AFIL';
			$rowtarea5[FECHATAREA]=$rset_monitoreo_24->MONITOREO_ARRIBO_AFIL;
			$rowtarea5[IDEXPEDIENTE]=$idexpediente;
			$rowtarea5[IDASISTENCIA]=$idasistencia;
			$rowtarea5[STATUSTAREA]='PENDIENTE';
			$rowtarea5[IDUSUARIO]=$idusuario;
			$rowtarea5[DISPLAY] = 0;
			$con->insert_reg('monitor_tarea',$rowtarea5);
			$fechaasignacion=$rset_monitoreo_24->FECHAASIGNACION;
		}
		echo $nummonitoreo;
		if($nummonitoreo>=2880){
			$sql_monitoreo_proveedor_24="SELECT SUBDATE(TEAT, INTERVAL 1440 MINUTE) MONITOREO_PROVEEDOR_24,
						     ADDDATE(SUBDATE(TEAT, INTERVAL 1140 MINUTE),INTERVAL $monitoreo_afiliado_programado MINUTE)  MONITOREO_AFILIADO_24
					      FROM asistencia_asig_proveedor 
					      WHERE IDASISTENCIA=$idasistencia AND STATUSPROVEEDOR='AC'";
			$exec_monitoreo_proveedor_24=$con->query($sql_monitoreo_proveedor_24);
			if($rset_monitoreo_proveedor_24 = $exec_monitoreo_proveedor_24->fetch_object()){
				$rowtareaprov[IDTAREA]='MON_PROV_24';
				$rowtareaprov[FECHATAREA]=$rset_monitoreo_proveedor_24->MONITOREO_PROVEEDOR_24;
				$rowtareaprov[IDEXPEDIENTE]=$idexpediente;
				$rowtareaprov[IDASISTENCIA]=$idasistencia;
				$rowtareaprov[RECORDATORIO]=1;
				$rowtareaprov[NUMMON]=0;
				$rowtareaprov[STATUSTAREA]='PENDIENTE';
				$rowtareaprov[IDUSUARIO]=$idusuario;
				$rowtareaprov[DISPLAY] = 0;
				$con->insert_reg('monitor_tarea',$rowtareaprov);

				$rowtareaafil[IDTAREA]='MON_AFIL_24';
				$rowtareaafil[FECHATAREA]=$rset_monitoreo_proveedor_24->MONITOREO_AFILIADO_24;
				$rowtareaafil[IDEXPEDIENTE]=$idexpediente;
				$rowtareaafil[IDASISTENCIA]=$idasistencia;
				$rowtareaafil[RECORDATORIO]=1;
				$rowtareaafil[STATUSTAREA]='PENDIENTE';
				$rowtareaafil[IDUSUARIO]=$idusuario;
				$rowtareaafil[DISPLAY] = 0;
				$con->insert_reg('monitor_tarea',$rowtareaafil);
			}

		}


	}else{
		$asis = new asistencia();
		$asis->carga_datos($idasistencia);
		//$prioridad=$_POST[PRIORIDAD];

		$asig[IDPROVEEDOR]=$idproveedor;
		$asig[STATUSPROVEEDOR]='AC';
		$asig[LOCALFORANEO]=$localforaneo;
		$asig[FECHAASIGNACION]=$fechaactual;
		$con->update("asistencia_asig_proveedor",$asig," WHERE IDASISTENCIA=".$idasistencia." AND IDPROVEEDOR = '0' AND STATUSPROVEEDOR='AC'");

		$asisuser[IDASISTENCIA]=$idasistencia;
		$asisuser[IDUSUARIO]=$idusuario;
		$asisuser[IDETAPA]=2;
		$con->insert_reg('asistencia_usuario',$asisuser);
		//ACTUALIZAMOS LA ETAPA
		$rows[IDETAPA]=3;
		$rows[ARRPRIORIDADATENCION]=$prioridad;
		$con->update("asistencia",$rows,"WHERE IDASISTENCIA=".$idasistencia);
		//STATUS DE TAREA ASIG_PROV
		$tareaup[STATUSTAREA] = 'ATENDIDA';
		$con->update("monitor_tarea",$tareaup," WHERE IDASISTENCIA=$idasistencia AND IDTAREA='ASIG_PROV'");

		$sql_monitoreo_24_con="SELECT ADDDATE(FECHAASIGNACION,INTERVAL $con_pro MINUTE) MONITOREO_DISPONIBILIDAD_AFILIADO from asistencia_asig_proveedor WHERE IDASISTENCIA=$idasistencia";
		echo $sql_monitoreo_24_con;
		$exec_monitoreo_24_con = $con->query($sql_monitoreo_24_con);
		if($rset_monitoreo_24_con=$exec_monitoreo_24_con->fetch_object())
		{
			$rowtarea11[IDTAREA]='CONF_SERV';
			$rowtarea11[FECHATAREA]=$rset_monitoreo_24_con->MONITOREO_DISPONIBILIDAD_AFILIADO;
			$rowtarea11[IDEXPEDIENTE]=$idexpediente;
			$rowtarea11[IDASISTENCIA]=$idasistencia;
			$rowtarea11[STATUSTAREA]='PENDIENTE';
			$rowtarea11[IDUSUARIO]=$idusuario;
			$rowtarea11[DISPLAY] = 0;
			$con->insert_reg('monitor_tarea',$rowtarea11);
		}

		foreach ($asis->proveedores as $prov)
		{

			if ($prov[statusproveedor]=='AC'){
				$proveedor_act= $prov[idproveedor];
				//$proveedor = new proveedor();
				//$proveedor->carga_datos($proveedor_act);
				$asig[TEAT] = $prov[teat];
				$asig[TEAM] = $prov[team];
				$fechaasignacion=$fechaactual;
				// $idasignacion=$prov[idasigprov];
			}


		}
	}
}


$etiqueta = ($prioridad=='EME')?'EMERGENCIA':'PROGRAMADO';


$asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
//$asis_bitacora[IDETAPA]=$_POST[IDETAPA];

$comentario="ASIGNACION DE PROVEEDOR\nPRIORIDAD ATENCION : $etiqueta\nTEAT : $asig[TEAT]\nTEAM : $asig[TEAM]\nFECHA ASIGNACION : $fechaasignacion\n\n$bitacora";

$asis_bitacora[COMENTARIO]=$comentario;
$asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];
$asis_bitacora[IDPROVEEDOR]=$_POST[IDPROVEEDOR];
$asis_bitacora[ARRCLASIFICACION]=$_POST[ARRCLASIFICACION];
$asis_bitacora[IDASIGPROV]=$idasigprov;

$con->insert_reg("$con->temporal.asistencia_bitacora_etapa2",$asis_bitacora);
?>
