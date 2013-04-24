<?
include_once('../../modelo/clase_mysqli.inc.php');

$con = new DB_mysqli();
$con->select_db($con->temporal);
$catalogo = $con->catalogo;


$idasistencia = $_POST[IDASISTENCIA];
$idusuario = $_POST[IDUSUARIOMOD];
$teat=$_POST[TEAT];
$team=$_POST[TEAM];
$comentario=$_POST[COMENTARIO];
$idservicio=$_POST[IDSERVICIO];
$prioridad=$_POST[PRIORIDAD];

//$asig[IDPROVEEDOR]=0;
$asig[IDASISTENCIA]=$idasistencia;
//$asig[STATUSPROVEEDOR]='AC';
//$asig[IDUSUARIOMOD]=$idusuario;
//$asig[LOCALFORANEO]=$localforaneo;



$asis_bitacora[IDASISTENCIA]=$_POST[IDASISTENCIA];
//$asis_bitacora[IDETAPA]=$_POST[IDETAPA];
$asis_bitacora[COMENTARIO]='REPROGRAMAR SERVICIO';
$asis_bitacora[IDUSUARIOMOD]=$_POST[IDUSUARIOMOD];


$con->insert_reg("$con->temporal.asistencia_bitacora_etapa3",$asis_bitacora);
    
$sql_asignacion="SELECT AP.IDASIGPROV,AP.TEAT,AP.TEAM,A.ARRPRIORIDADATENCION FROM asistencia_asig_proveedor AP INNER JOIN asistencia A 
 ON AP.IDASISTENCIA = A.IDASISTENCIA WHERE AP.IDASISTENCIA=$idasistencia AND AP.STATUSPROVEEDOR='AC'";
echo $sql_asignacion;
    $exec_asignacion=$con->query($sql_asignacion);
    if($rset_asignacion=$exec_asignacion->fetch_object()){
	  $idasignacion = $rset_asignacion->IDASIGPROV;
	  $teatasig=$rset_asignacion->TEAT;
	  $teamasig=$rset_asignacion->TEAM;
	  $atencion=$rset_asignacion->ARRPRIORIDADATENCION;
    }

//echo $atencion;
if($atencion=='EME'){

    $asig[TEAT]=$teat;
    $asig[TEAM]=$team;
    //CANCELAMOS SI EXISTE UN PROVEEDOR ACTIVO
    //$rowsu['STATUSPROVEEDOR'] = "CA";
   // $update=$con->update("asistencia_asig_proveedor",$rowsu,"WHERE IDASISTENCIA=".$idasistencia);
    //ASIGNACION DE PROVEEDOR
    $con->update('asistencia_asig_proveedor',$asig," WHERE IDASISTENCIA=$idasistencia AND STATUSPROVEEDOR='AC'");
     
    //CANCELAMOS SI EXISTE UN PROVEEDOR ACTIVO
    
    
   /* $asiglog[IDASISTENCIA]=$idasistencia;
    $asiglog[TEAT]=$asig[TEAT];
    $asiglog[TEAM]=$asig[TEAM];
    $asiglog[IDUSUARIOMOD]=$idusuario;
    $asiglog[MOTIVO]='ASIGNACION';
    $con->insert_reg('asistencia_asignacion_tiempo_log',$asiglog); */
  
    $asiglog[IDASISTENCIA]=$idasistencia;
    $asiglog[TEATASIG]=$teatasig;
    $asiglog[TEAMASIG]=$teamasig;
    $asiglog[IDASIGPROV]=$idasignacion;
    $asiglog[TEAT]=$teat;
    $asiglog[TEAM]=$team;
    $asiglog[IDUSUARIOMOD]=$idusuario;
    $asiglog[MOTIVO]='REPROGRAMACION';
    $asiglog[ARRPRIORIDADATENCION]=$prioridad;
    $con->insert_reg('asistencia_asignacion_reprogramado',$asiglog);

    $jus[IDASISTENCIA]=$idasistencia;
    $jus[IDJUSTIFICACION]=10;
    $jus[IDUSUARIOMOD]=$idusuario;
    $jus[MOTIVO]=$comentario;
    $con->insert_reg('asistencia_justificacion',$jus);
    //REGISTRAMOS MOVIMIENTO DE USUARIO
   /* $asisuser[IDASISTENCIA]=$idasistencia;
    $asisuser[IDUSUARIO]=$idusuario;
    $asisuser[IDETAPA]=2;
    $con->insert_reg('asistencia_usuario',$asisuser);
    //ACTUALIZAMOS LA ETAPA
    $rows[IDETAPA]=3;
    $rows[ARRPRIORIDADATENCION]=$prioridad;
    $con->update("asistencia",$rows,"WHERE IDASISTENCIA=".$idasistencia);
    //STATUS DE TAREA ASIG_PROV
    $tareaup[STATUSTAREA] = 'ATENDIDA';
    $con->update("monitor_tarea",$tareaup," WHERE IDASISTENCIA=$idasistencia AND IDTAREA='ASIG_PROV'");*/
    
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
    $sql_tarea ="SELECT A.IDEXPEDIENTE,A.IDASISTENCIA,AP.FECHAHORA,AP.TEAT,AP.TEAM,
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
	  $fechaasignacion = $rset_tarea->FECHAHORA;
	  $fechamonafil = $rset_tarea->MONAFIL;
	  $minutomonitoreo = $rset_tarea->NUM;
    }
//INSERTAMOS LOS MONITOREOS COMO TAREAS PENDIENTES
    for($i=1;$i<=$nummonitoreo;$i++){
	$sql_monitoreo="SELECT ADDDATE(FECHAHORA, INTERVAL $minutomonitoreo*$i MINUTE) MONITOREO FROM asistencia_asig_proveedor
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
}
elseif($atencion=='PRO'){

    $asig[TEAT]=$teat;
    $asig[TEAM]=$team;
    //CANCELAMOS SI EXISTE UN PROVEEDOR ACTIVO
    //$rowsu['STATUSPROVEEDOR'] = "CA";
   // $update=$con->update("asistencia_asig_proveedor",$rowsu,"WHERE IDASISTENCIA=".$idasistencia);
    //ASIGNACION DE PROVEEDOR
    $con->update('asistencia_asig_proveedor',$asig," WHERE IDASISTENCIA=$idasistencia AND STATUSPROVEEDOR='AC'");
    
    
    $asiglog[IDASISTENCIA]=$idasistencia;
    $asiglog[TEATASIG]=$teatasig;
    $asiglog[TEAMASIG]=$teamasig;
    $asiglog[IDASIGPROV]=$idasignacion;
    $asiglog[TEAT]=$teat;
    $asiglog[TEAM]=$team;
    $asiglog[IDUSUARIOMOD]=$idusuario;
    $asiglog[MOTIVO]='REPROGRAMACION';
    $con->insert_reg('asistencia_asignacion_reprogramado',$asiglog);

    $jus[IDASISTENCIA]=$idasistencia;
    $jus[IDJUSTIFICACION]=10;
    $jus[IDUSUARIOMOD]=$idusuario;
    $jus[MOTIVO]=$comentario;
    $con->insert_reg('asistencia_justificacion',$jus);
//REGISTRAMOS MOVIMIENTO DE USUARIO
    //$asisuser[IDASISTENCIA]=$idasistencia;
    //$asisuser[IDUSUARIO]=$idusuario;
    //$asisuser[IDETAPA]=2;
    //$con->insert_reg('asistencia_usuario',$asisuser);
    //ACTUALIZAMOS LA ETAPA
    // $rows[IDETAPA]=3;
    //$rows[ARRPRIORIDADATENCION]=$prioridad;
    //$con->update("asistencia",$rows,"WHERE IDASISTENCIA=".$idasistencia);
    //STATUS DE TAREA ASIG_PROV
    $tarea[STATUSTAREA]='CANCELADA';
    $con->update("monitor_tarea",$tarea," WHERE IDASISTENCIA=".$idasistencia." AND STATUSTAREA='PENDIENTE'");
    
    $monprog = $con->lee_parametro('MONITOREO_PROGRAMADO');
    
    $sql_tarea="SELECT A.IDEXPEDIENTE,A.IDASISTENCIA,AP.FECHAHORA,AP.TEAT,AP.TEAM,
      ADDDATE(AP.TEAM, INTERVAL 2 MINUTE) MONAFIL,
      SUBDATE(AP.TEAT,INTERVAL 60 MINUTE) TEAT_1,
      ROUND(IF(HOUR(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT))>0,
      (HOUR(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT))*60)/1+MINUTE(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT)),
      MINUTE(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT))),0) MEDIA,
      ROUND(IF(HOUR(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT))>0,
      (HOUR(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT))*60)/1+MINUTE(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT)),
      MINUTE(TIMEDIFF(SUBDATE(AP.TEAT,INTERVAL 60 MINUTE),AP.TEAT)))/$monprog,0) NUM
      FROM asistencia_asig_proveedor AP left JOIN 
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
	  $fechaasignacion = $rset_tarea->FECHAHORA;
	  $fechamonafil = $rset_tarea->MONAFIL;
	  $minutomonitoreo = $rset_tarea->NUM;
    }
  /*  $rowtarea3[IDTAREA]='ASIG_PROV';
  $rowtarea3[FECHATAREA]=$fechamonafil;
  $rowtarea3[IDEXPEDIENTE]=$idexpediente;
  $rowtarea3[IDASISTENCIA]=$idasistencia;
  $rowtarea3[RECORDATORIO]=1;
  $rowtarea3[STATUSTAREA]='PENDIENTE';
  $rowtarea3[IDUSUARIO]=$idusuario;
  $rowtarea3[DISPLAY] = 0;
  $con->insert_reg('monitor_tarea',$rowtarea3);
*/
    for($i=1;$i<=$monprog;$i++){
	$sql_monitoreo="SELECT ADDDATE(SUBDATE(TEAT,INTERVAL 60 MINUTE), INTERVAL $minutomonitoreo*$i MINUTE) MONITOREO FROM asistencia_asig_proveedor
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
}