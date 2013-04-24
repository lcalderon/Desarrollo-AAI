<?
include_once('../../modelo/clase_mysqli.inc.php');

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
    
    $monprog = $con->lee_parametro('MONITOREO_PROGRAMADO');
    
    $sql_tarea="SELECT A.IDEXPEDIENTE,A.IDASISTENCIA,AP.FECHAASIGNACION,AP.TEAT,AP.TEAM,
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
    echo $sql_tarea;
      $exec_tarea=$con->query($sql_tarea);
    while($rset_tarea=$exec_tarea->fetch_object())
     {
	  $idexpediente = $rset_tarea->IDEXPEDIENTE;
	  $idasistencia = $rset_tarea->IDASISTENCIA;
	  $fechaasignacion = $rset_tarea->FECHAASIGNACION;
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
}




?>
