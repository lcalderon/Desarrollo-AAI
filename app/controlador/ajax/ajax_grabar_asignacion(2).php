<?
include_once('../../modelo/clase_lang.inc.php');
include_once('../../modelo/clase_mysqli.inc.php');
include_once('../../modelo/clase_ubigeo.inc.php');
include_once('../../modelo/clase_moneda.inc.php');
include_once('../../modelo/clase_plantilla.inc.php');
include_once('../../modelo/clase_persona.inc.php');
include_once('../../modelo/clase_telefono.inc.php');
include_once('../../modelo/clase_cuenta.inc.php');
include_once('../../modelo/clase_familia.inc.php');
include_once('../../modelo/clase_servicio.inc.php');
include_once('../../modelo/clase_programa_servicio.inc.php');
include_once('../../modelo/clase_programa.inc.php');
include_once('../../modelo/clase_afiliado.inc.php');
include_once('../../modelo/clase_etapa.inc.php');
include_once('../../modelo/clase_contacto.inc.php');
include_once('../../modelo/clase_poligono.inc.php');
include_once('../../modelo/clase_circulo.inc.php');
include_once('../../modelo/clase_proveedor.inc.php');
include_once('../../modelo/clase_expediente.inc.php');
include_once('../../modelo/clase_asistencia.inc.php');

$con = new DB_mysqli();
$con->select_db($con->temporal);
$catalogo = $con->catalogo;


$idasistencia = $_POST[IDASISTENCIA];
$idusuario = $_POST[IDUSUARIOMOD];
$idproveedor =$_POST[IDPROVEEDOR];
$fechaactual = date('Y-m-d H:i:s');
$minutos = $_POST[MINUTOS];
$prioridad = $_POST[PRIORIDAD];
$idservicio = $_POST[IDSERVICIO];
$localforaneo=$_POST[LOCALFORANEO];
$teat=$_POST[TEAT];
$team=$_POST[TEAM];
$nombre = $_POST[NOMBRE];
$comentario = $_POST[COMENTARIO];
$bitacora=$_POST[COMENTARIOBITACORA];

$asig[IDPROVEEDOR]=$idproveedor;
$asig[IDASISTENCIA]=$idasistencia;
$asig[STATUSPROVEEDOR]='AC';
$asig[IDUSUARIOMOD]=$idusuario;
$asig[LOCALFORANEO]=$localforaneo;

$con_eme=$con->lee_parametro('TIEMPO_CONTACTO_EMERGENCIA');
$con_pro=$con->lee_parametro('TIEMPO_CONTACTO_PROGRAMADO');

/*

$con->insert_reg("$con->temporal.asistencia_bitacora_etapa2",$asis_bitacora);
*/
//echo $prioridad;

if($prioridad=='EME'){
  //CALCULO TEAT = FECHAACTUAL TEAM = TEAT + MINUTOS
    $sql_team="SELECT ADDDATE('$fechaactual', INTERVAL $minutos MINUTE) TEAM,ADDDATE('$fechaactual', INTERVAL $con_eme  MINUTE) TIEMPO_CONTACTO";
   echo $sql_team;
    $exec_team=$con->query($sql_team);
    if($rset_team=$exec_team->fetch_object()){ 
	$asig[TEAM] = $rset_team->TEAM; 
	$tiempo_contacto=$rset_team->TIEMPO_CONTACTO;
    }
	      

    //CANCELAMOS SI EXISTE UN PROVEEDOR ACTIVO
    $rowsu['STATUSPROVEEDOR'] = "CA";
    $update=$con->update("asistencia_asig_proveedor",$rowsu,"WHERE IDASISTENCIA=".$idasistencia." AND STATUSPROVEEDOR='AC'");
    //ASIGNACION DE PROVEEDOR
    $asig[FECHAASIGNACION]=$fechaactual;
    $asig[TEAT]=$fechaactual;
    $con->insert_reg('asistencia_asig_proveedor',$asig);
    
   /* $asiglog[IDASISTENCIA]=$idasistencia;
    $asiglog[TEAT]=$asig[TEAT];
    $asiglog[TEAM]=$asig[TEAM];
    $asiglog[IDUSUARIOMOD]=$idusuario;
    $asiglog[MOTIVO]='ASIGNACION';
    $con->insert_reg('asistencia_asignacion_tiempo_log',$asiglog); */

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
    $con->update("monitor_tarea",$tareaup," WHERE IDASISTENCIA=$idasistencia AND IDTAREA='ASIG_PROV'");
    $tarea[STATUSTAREA]='CANCELADA';
    $con->update("monitor_tarea",$tarea," WHERE IDASISTENCIA=".$idasistencia." AND STATUSTAREA='PENDIENTE'");
    //OBTENEMOS EL NUMERO DE MONITOREOS DEPEDNDIENDO DEL SERVICIO
    $sql_servicio="SELECT NUMMONITOREO FROM $catalogo.catalogo_servicio WHERE IDSERVICIO = $idservicio";
   // echo $sql_servicio;
    $exec_servicio = $con->query($sql_servicio);
    if($rset_servicio=$exec_servicio->fetch_object())
    {
	$nummonitoreo =$rset_servicio->NUMMONITOREO;
    }
    //CALCULAMOS LOS TIEMPOS DE LOS MONITOREOS
    $sql_tarea ="SELECT A.IDEXPEDIENTE,A.IDASISTENCIA,AP.FECHAASIGNACION,AP.TEAT,AP.TEAM,
ADDDATE(AP.TEAM, INTERVAL 2 MINUTE) MONAFIL,
TIMEDIFF(AP.TEAT,AP.TEAM),
ROUND(IF(HOUR(TIMEDIFF(AP.TEAT,AP.TEAM))>0,(HOUR(TIMEDIFF(AP.TEAT,AP.TEAM))*60)/1+MINUTE(TIMEDIFF(AP.TEAT,AP.TEAM)),MINUTE(TIMEDIFF(AP.TEAT,AP.TEAM))),0) MEDIA,
 ROUND(IF(HOUR(TIMEDIFF(AP.TEAT,AP.TEAM))>0,(HOUR(TIMEDIFF(AP.TEAT,AP.TEAM))*60)/1+MINUTE(TIMEDIFF(AP.TEAT,AP.TEAM)),MINUTE(TIMEDIFF(AP.TEAT,AP.TEAM)))/$nummonitoreo,0) NUM
 FROM asistencia_asig_proveedor AP INNER JOIN 
 $catalogo.catalogo_proveedor P
 ON AP.IDPROVEEDOR = P.IDPROVEEDOR 
INNER JOIN asistencia A
ON A.IDASISTENCIA = AP.IDASISTENCIA 
WHERE AP.IDASISTENCIA = $idasistencia
AND AP.STATUSPROVEEDOR IN ('AC')";
//echo $sql_tarea;
    $exec_tarea=$con->query($sql_tarea);
    while($rset_tarea=$exec_tarea->fetch_object())
     {
	  $idexpediente = $rset_tarea->IDEXPEDIENTE;
	  $idasistencia = $rset_tarea->IDASISTENCIA;
	  $fechaasignacion = $rset_tarea->FECHAASIGNACION;
	  $fechamonafil = $rset_tarea->MONAFIL;
	  $minutomonitoreo = $rset_tarea->NUM;
    }

	      $rowtarea4[IDTAREA]='CON_AFIL';
	      $rowtarea4[FECHATAREA]=$tiempo_contacto;
	      $rowtarea4[IDEXPEDIENTE]=$idexpediente;
	      $rowtarea4[IDASISTENCIA]=$idasistencia;
	      $rowtarea4[STATUSTAREA]='PENDIENTE';
	      $rowtarea4[IDUSUARIO]=$idusuario;
	      $rowtarea4[DISPLAY] = 1;
	      $con->insert_reg('monitor_tarea',$rowtarea4);

//INSERTAMOS LOS MONITOREOS COMO TAREAS PENDIENTES
    for($i=1;$i<=$nummonitoreo;$i++){
	$sql_monitoreo="SELECT ADDDATE(FECHAASIGNACION, INTERVAL $minutomonitoreo*$i MINUTE) MONITOREO FROM asistencia_asig_proveedor
	WHERE IDASISTENCIA = $idasistencia AND STATUSPROVEEDOR = 'AC'";

	$exec_monitoreo = $con->query($sql_monitoreo);
	if($rset_monitoreo=$exec_monitoreo->fetch_object()){
	   if($i<$nummonitoreo){
		$rowtareaprov[IDTAREA]='MON_PROV';
	    }else{
		$rowtareaprov[IDTAREA]='ARR_PROV';
	    }
	    $rowtareaprov[FECHATAREA]=$rset_monitoreo->MONITOREO;
	    $rowtareaprov[IDEXPEDIENTE]=$idexpediente;
	    $rowtareaprov[IDASISTENCIA]=$idasistencia;
	    $rowtareaprov[RECORDATORIO]=1;
	    $rowtareaprov[NUMMON]=$i;
	    $rowtareaprov[STATUSTAREA]='PENDIENTE';
	    $rowtareaprov[IDUSUARIO]=$idusuario;
	    $con->insert_reg('monitor_tarea',$rowtareaprov);

	}
    
   
    }
  $rowtarea3[IDTAREA]='MON_AFIL';
  $rowtarea3[FECHATAREA]=$fechamonafil;
  $rowtarea3[IDEXPEDIENTE]=$idexpediente;
  $rowtarea3[IDASISTENCIA]=$idasistencia;
  $rowtarea3[RECORDATORIO]=1;
  $rowtarea3[STATUSTAREA]='PENDIENTE';
  $rowtarea3[IDUSUARIO]=$idusuario;
  $con->insert_reg('monitor_tarea',$rowtarea3);
}elSeif($prioridad=='PRO'){
      
    //VERIFICAMOS SI EXISTE UN PROVEEDOR
    $sql_prov="SELECT * from asistencia_asig_proveedor WHERE IDASISTENCIA = $idasistencia AND IDPROVEEDOR ='0' AND STATUSPROVEEDOR='AC'";
    $exec_prov = $con->query($sql_prov);
    $nreg_prov= $exec_prov->num_rows;
    if($nreg_prov==0){
		
		//CANCELAMOS SI EXISTE UN PROVEEDOR ACTIVO
		$rowsu['STATUSPROVEEDOR'] = "CA";
		$update=$con->update("asistencia_asig_proveedor",$rowsu,"WHERE IDASISTENCIA=".$idasistencia." AND STATUSPROVEEDOR='AC'");
		//ASIGNACION DE PROVEEDOR
		$asig[TEAT]=$teat;
		$asig[TEAM]=$team;
		$asig[FECHAASIGNACION]=$fechaactual;
		//echo $asig[TEAT];
		//echo $asig[TEAM];
		$con->insert_reg('asistencia_asig_proveedor',$asig);
  
		/*$asiglog[IDASISTENCIA]=$idasistencia;
		$asiglog[TEAT]=$asig[TEAT];
		$asiglog[TEAM]=$asig[TEAM];
		$asiglog[IDUSUARIOMOD]=$idusuario;
		$asiglog[MOTIVO]='ASIGNACION';
		$con->insert_reg('asistencia_asignacion_tiempo_log',$asiglog);
	      */
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
		 $tarea[STATUSTAREA]='CANCELADA';
		$con->update("monitor_tarea",$tarea," WHERE IDASISTENCIA=".$idasistencia." AND STATUSTAREA='PENDIENTE'");
		$monprog = $con->lee_parametro('MONITOREO_PROGRAMADO');
		
		$sql_tarea="SELECT A.IDEXPEDIENTE,A.IDASISTENCIA,AP.FECHAASIGNACION,AP.TEAT,AP.TEAM,
		  ADDDATE('$fechaactual', INTERVAL $con_pro MINUTE) TIEMPO_CONTACTO,
		  ADDDATE(AP.TEAM, INTERVAL 2 MINUTE) MONAFIL,
		  SUBDATE(AP.TEAT,INTERVAL 60 MINUTE) TEAT_1,
		  ROUND(IF(HOUR(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT))>0,
		  (HOUR(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT))*60)/1+MINUTE(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT)),
		  MINUTE(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT))),0) MEDIA,
		  ROUND(IF(HOUR(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT))>0,
		  (HOUR(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT))*60)/1+MINUTE(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT)),
		  MINUTE(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT)))/$monprog,0) NUM
		  FROM asistencia_asig_proveedor AP INNER JOIN 
		  $catalogo.catalogo_proveedor P
		  ON AP.IDPROVEEDOR = P.IDPROVEEDOR 
		  INNER JOIN asistencia A
		  ON A.IDASISTENCIA = AP.IDASISTENCIA 
		  WHERE AP.IDASISTENCIA = $idasistencia
		  AND AP.STATUSPROVEEDOR IN ('AC')";
		  $exec_tarea=$con->query($sql_tarea);
		while($rset_tarea=$exec_tarea->fetch_object())
		{
		      $idexpediente = $rset_tarea->IDEXPEDIENTE;
		      $idasistencia = $rset_tarea->IDASISTENCIA;
		      $fechaasignacion = $rset_tarea->FECHAASIGNACION;
		      $fechamonafil = $rset_tarea->MONAFIL;
		      $minutomonitoreo = $rset_tarea->NUM;
		      $tiempo_contacto=$rset_tarea->TIEMPO_CONTACTO;
		}
	      $rowtarea4[IDTAREA]='CON_AFIL';
	      $rowtarea4[FECHATAREA]=$tiempo_contacto;
	      $rowtarea4[IDEXPEDIENTE]=$idexpediente;
	      $rowtarea4[IDASISTENCIA]=$idasistencia;
	      $rowtarea4[STATUSTAREA]='PENDIENTE';
	      $rowtarea4[IDUSUARIO]=$idusuario;
	      $rowtarea4[DISPLAY] = 1;
	      $con->insert_reg('monitor_tarea',$rowtarea4);

		for($i=1;$i<=$monprog;$i++){
		    $sql_monitoreo="SELECT ADDDATE(SUBDATE(TEAT,INTERVAL 60 MINUTE), INTERVAL $minutomonitoreo*$i MINUTE) MONITOREO FROM asistencia_asig_proveedor
		    WHERE IDASISTENCIA = $idasistencia AND STATUSPROVEEDOR = 'AC'";

		    $exec_monitoreo = $con->query($sql_monitoreo);
		    if($rset_monitoreo=$exec_monitoreo->fetch_object()){
			if($i<$monprog){
			      $rowtareaprov[IDTAREA]='MON_PROV';
			  }else{
			      $rowtareaprov[IDTAREA]='ARR_PROV';
			  }
			$rowtareaprov[FECHATAREA]=$rset_monitoreo->MONITOREO;
			$rowtareaprov[IDEXPEDIENTE]=$idexpediente;
			$rowtareaprov[IDASISTENCIA]=$idasistencia;
			$rowtareaprov[RECORDATORIO]=1;
			$rowtareaprov[NUMMON]=$i;
			$rowtareaprov[STATUSTAREA]='PENDIENTE';
			$rowtareaprov[IDUSUARIO]=$idusuario;
			$rowtareaprov[DISPLAY] = 0;
			$con->insert_reg('monitor_tarea',$rowtareaprov);
		    }
		
	      
		}
		$rowtarea3[IDTAREA]='MON_AFIL';
	      $rowtarea3[FECHATAREA]=$fechamonafil;
	      $rowtarea3[IDEXPEDIENTE]=$idexpediente;
	      $rowtarea3[IDASISTENCIA]=$idasistencia;
	      $rowtarea3[RECORDATORIO]=1;
	      $rowtarea3[STATUSTAREA]='PENDIENTE';
	      $rowtarea3[IDUSUARIO]=$idusuario;
	      $rowtarea3[DISPLAY] = 0;
	      $con->insert_reg('monitor_tarea',$rowtarea3);
    }else{
	$asis = new asistencia();
	$asis->carga_datos($idasistencia);
	//$prioridad=$_POST[PRIORIDAD];

      

	$asig[IDPROVEEDOR]=$idproveedor;
	$asig[STATUSPROVEEDOR]='AC';
	$asig[LOCALFORANEO]=$localforaneo;
	$asig[FECHAASIGNACION]=$fechaactual;
	$con->update("asistencia_asig_proveedor",$asig,"WHERE IDASISTENCIA=".$idasistencia." AND IDPROVEEDOR = '0' AND STATUSPROVEEDOR='AC'");
	
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

       /* $mov[IDPROVEEDOR]=$idproveedor;
	$mov[IDASISTENCIA]=$idasistencia;
	$mov[ARRMOV]='ASIG_PROV';
	$mov[USUARIOMOD]=$idusuario;
	$con->insert_reg("asistencia_movimiento_tiempo",$mov);
*/

if($prioridad == 'EME'){ $etiqueta = 'EMERGENCIA'; }elseif($prioridad=='PRO'){ $etiqueta='PROGRAMADO'; }
$asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
//$asis_bitacora[IDETAPA]=$_POST[IDETAPA];

$comentario="ASIGNACION DE PROVEEDOR\nPRIORIDAD ATENCION : $etiqueta\nTEAT : $asig[TEAT]\nTEAM : $asig[TEAM]\nFECHA ASIGNACION : $fechaasignacion\n\n$bitacora";

$asis_bitacora[COMENTARIO]=$comentario;
$asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];
$asis_bitacora[IDPROVEEDOR]=$_POST[IDPROVEEDOR];

$con->insert_reg("$con->temporal.asistencia_bitacora_etapa2",$asis_bitacora);
?>
