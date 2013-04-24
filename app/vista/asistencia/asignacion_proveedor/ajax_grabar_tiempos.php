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


$idasistencia = $_POST[IDASISTENCIA];
$idusuario = $_POST[IDUSUARIOMOD];
$idproveedor =$_POST[IDPROVEEDOR];
$fechaactual = date('Y-m-d H:i:s');
//$minutos = $_POST[MINUTOS];
$prioridad = $_POST[PRIORIDAD];
$idservicio = $_POST[IDSERVICIO];
$bitacora=$_POST[COMENTARIOBITACORA];
//$localforaneo=$_POST[LOCALFORANEO];
$teat=$_POST[TEAT];
$team=$_POST[TEAM];

$asig[IDPROVEEDOR]=0;
$asig[IDASISTENCIA]=$idasistencia;
$asig[STATUSPROVEEDOR]='AC';
$asig[IDUSUARIOMOD]=$idusuario;
//$asig[LOCALFORANEO]=$localforaneo;


/*
$asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
//$asis_bitacora[IDETAPA]=$_POST[IDETAPA];
$asis_bitacora[COMENTARIO]='REGISTRO DE TEAT - TEAM ( PROGRAMADO )';
$asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];


$con->insert_reg("$con->temporal.asistencia_bitacora_etapa2",$asis_bitacora);
*/

$con_pro=$con->lee_parametro('TIEMPO_CONTACTO_PROGRAMADO');

$monitoreo_afiliado_arribo_programado=$con->lee_parametro('TIEMPO_MONITOREO_AFILIADO_ARRIBO_PROGRAMADO');

if($prioridad=='PRO'){
    $rowsu['STATUSPROVEEDOR'] = "CA";
    $update=$con->update("asistencia_asig_proveedor",$rowsu,"WHERE IDASISTENCIA=".$idasistencia." AND STATUSPROVEEDOR='AC'");

    $asig[TEAT]=$teat;
    $asig[TEAM]=$team;
     $asig[STATUSPROVEEDOR]='AC';
     //$asig[FECHAASIGNACION]=$fechaactual;
    //CANCELAMOS SI EXISTE UN PROVEEDOR ACTIVO
   
    //ASIGNACION DE PROVEEDOR

    if($idproveedor=='0'){
	  $asigu[TEAT]=$teat;
	  $asigu[TEAM]=$team; 

	$con->update('asistencia_asig_proveedor',$asigu," WHERE IDASISTENCIA=$idasistencia AND STATUSPROVEEDOR='AC' AND IDPROVEEDOR=0");
    }else{
	$con->insert_reg('asistencia_asig_proveedor',$asig);
    }
     
	
	
    
    //REGISTRAMOS MOVIMIENTO DE USUARIO
    //$asisuser[IDASISTENCIA]=$idasistencia;
    //$asisuser[IDUSUARIO]=$idusuario;
    //$asisuser[IDETAPA]=2;
    //$con->insert_reg('asistencia_usuario',$asisuser);
    //ACTUALIZAMOS LA ETAPA
    // $rows[IDETAPA]=3;
    $rows[ARRPRIORIDADATENCION]=$prioridad;
    $con->update("asistencia",$rows,"WHERE IDASISTENCIA=".$idasistencia);
    //STATUS DE TAREA ASIG_PROV
    $tarea[STATUSTAREA]='CANCELADA';
    $con->update("monitor_tarea",$tarea," WHERE IDASISTENCIA=".$idasistencia." AND STATUSTAREA='PENDIENTE'");
    
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


			/*$rowtarea1[IDTAREA]='CON_AFIL';
			$rowtarea1[FECHATAREA]=$rset_monitoreo_24->MONITOREO_DISPONIBILIDAD_AFILIADO;
			$rowtarea1[IDEXPEDIENTE]=$idexpediente;
			$rowtarea1[IDASISTENCIA]=$idasistencia;
			$rowtarea1[STATUSTAREA]='PENDIENTE';
			$rowtarea1[IDUSUARIO]=$idusuario;
			$rowtarea1[DISPLAY] = 0;
			$con->insert_reg('monitor_tarea',$rowtarea1);*/
			
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
			
			$rowtarea5[IDTAREA]='ARR_AFIL';
			$rowtarea5[FECHATAREA]=$rset_monitoreo_24->MONITOREO_ARRIBO_AFIL;
			$rowtarea5[IDEXPEDIENTE]=$idexpediente;
			$rowtarea5[IDASISTENCIA]=$idasistencia;
			$rowtarea5[STATUSTAREA]='PENDIENTE';
			$rowtarea5[IDUSUARIO]=$idusuario;
			$rowtarea5[DISPLAY] = 0;
			$con->insert_reg('monitor_tarea',$rowtarea5);
			$fechaasignacion=$rset_monitoreo_24->FECHAASIGNACION;
		  }
//echo $nummonitoreo;
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
}

if($prioridad == 'EME'){ $etiqueta = 'EMERGENCIA'; }elseif($prioridad=='PRO'){ $etiqueta='PROGRAMADO'; }
$asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
//$asis_bitacora[IDETAPA]=$_POST[IDETAPA];

$comentario="ASIGNACION DE TIEMPOS\nPRIORIDAD ATENCION : $etiqueta\nTEAT : $asig[TEAT]\nTEAM : $asig[TEAM]\nFECHA ASIGNACION : $fechaasignacion\n\n$bitacora";

$asis_bitacora[COMENTARIO]=$comentario;
$asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];
$asis_bitacora[IDPROVEEDOR]=$_POST[IDPROVEEDOR];

$con->insert_reg("$con->temporal.asistencia_bitacora_etapa2",$asis_bitacora);


?>
