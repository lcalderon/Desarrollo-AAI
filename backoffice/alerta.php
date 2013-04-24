<?php

include_once('../app/modelo/clase_mysqli.inc.php');
include_once('../librerias/xmpphp/XMPPHP/XMPP.php');

$con = new DB_mysqli();

// activate full error reporting
//error_reporting(E_ALL & E_STRICT);
$con->select_db($con->catalogo);
$db1=$con->temporal;
$date = date('Y-m-d H:i:s');
/*
$conn = new XMPPHP_XMPP('192.168.0.180', 5222, 'soaang_admin', 'jdb63jnfd839ohf', 'xmpphp', '192.168.0.180', $printlog=true, $loglevel=XMPPHP_Log::LEVEL_INFO);
try {
    $conn->use_encryption = False;
    $conn->connect();
    $conn->processUntil('session_start');
    $conn->presence();
    $conn->message('pquispe@aacorp01', 'RECORDATORIO: '.$TAREA.' ASISTENCIA: '.$ASISTENCIA.' https://'.$ip.'/app/vista/expediente/entrada/expediente_frmexpediente.php?idexped='.$EXPED.'&origen=XMPPHP');
    $conn->disconnect();
//    $update_alerta = "update $db1.monitor_tarea set RECORDATORIOENVIADO = 1,FECHARECORDATORIO='".$FECHARECORDATORIO."' where ID = $ID";
 //   $exec_update_alerta = $con->query($update_alerta);
    

} catch(XMPPHP_Exception $e) {
    die($e->getMessage());
}
*/

//echo $date;


	$sql_monitor_recordatorio="SELECT MT.IDTAREA,CT.DESCRIPCION,MT.FECHATAREA,MT.IDUSUARIO,MT.IDASISTENCIA,CU.CHAT,MT.ID,SUBTIME(MT.FECHATAREA,'0:5:0') FECHARECORDATORIOMAXIMA,SUBTIME(MT.FECHATAREA,'0:5:2') FECHARECORDATORIO FROM $db1.monitor_tarea MT INNER JOIN
	catalogo_tarea CT ON MT.IDTAREA = CT.IDTAREA INNER JOIN catalogo_usuario CU ON MT.IDUSUARIO = CU.IDUSUARIO
	 WHERE MT.RECORDATORIO ='1' AND MT.RECORDATORIOENVIADO = 0 and MT.FECHATAREA >= '".$date."' ORDER BY MT.FECHATAREA ASC LIMIT 1";
	//echo $sql_monitor_recordatorio;
	 $exec_monitor_recordatorio = $con->query($sql_monitor_recordatorio);
	 while($rset_monitor_recordatorio=$exec_monitor_recordatorio->fetch_object())
	 {
	 	$TAREA =$rset_monitor_recordatorio->DESCRIPCION;
		$FECHA= $rset_monitor_recordatorio->FECHATAREA;
		$FECHARECORDATORIO= $rset_monitor_recordatorio->FECHARECORDATORIO;
		$FECHARECORDATORIOMAXIMA= $rset_monitor_recordatorio->FECHARECORDATORIOMAXIMA;
		$USUARIO= $rset_monitor_recordatorio->IDUSUARIO;
		$CHAT= $rset_monitor_recordatorio->CHAT;
		$ASISTENCIA =$rset_monitor_recordatorio->IDASISTENCIA;
		$ID = $rset_monitor_recordatorio->ID;
	 }


if($date >= $FECHARECORDATORIO && $date <= $FECHARECORDATORIOMAXIMA){
$conn = new XMPPHP_XMPP('192.168.0.180', 5222, 'soaang_admin', 'jdb63jnfd839ohf', 'xmpphp', '192.168.0.180', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
try {
    $conn->use_encryption = False;
    $conn->connect();
    $conn->processUntil('session_start');
    $conn->presence();
    $conn->message($CHAT, 'RECORDATORIO: '.$TAREA.' ASISTENCIA: '.$ASISTENCIA.' https://'.$ip.'/app/vista/expediente/entrada/expediente_frmexpediente.php?idexped='.$EXPED.'&origen=XMPPHP');
    $conn->disconnect();
    $update_alerta = "update $db1.monitor_tarea set RECORDATORIOENVIADO = 1,FECHARECORDATORIO='".$FECHARECORDATORIO."' where ID = $ID";
    $exec_update_alerta = $con->query($update_alerta);
    

} catch(XMPPHP_Exception $e) {
    die($e->getMessage());
}
}

$sql_monitor_alarma="SELECT MT.IDTAREA,CT.DESCRIPCION,MT.FECHATAREA,MT.IDUSUARIO,MT.IDASISTENCIA,CU.CHAT,CU.IDCARGO,MT.ID,ADDTIME(MT.FECHATAREA,'0:0:2') FECHAALARMAMAXIMA FROM $db1.monitor_tarea MT INNER JOIN
	catalogo_tarea CT ON MT.IDTAREA = CT.IDTAREA INNER JOIN catalogo_usuario CU ON MT.IDUSUARIO = CU.IDUSUARIO
	 WHERE MT.ALARMA ='1' AND MT.ALARMAENVIADO = 0 and MT.FECHATAREA >= '".$date."' ORDER BY MT.FECHATAREA ASC LIMIT 1";
	  $exec_monitor_alarma = $con->query($sql_monitor_alarma);
	 while($rset_monitor_alarma=$exec_monitor_alarma->fetch_object())
	 {
	 	$ATAREA =$rset_monitor_alarma->DESCRIPCION;
		$FECHA= $rset_monitor_alarma->FECHATAREA;
		$FECHAALARMAMAXIMA= $rset_monitor_alarma->FECHAALARMAMAXIMA;
		$USUARIO= $rset_monitor_alarma->IDUSUARIO;
		$CHAT= $rset_monitor_alarma->CHAT;
		$ASISTENCIA =$rset_monitor_alarma->IDASISTENCIA;
		$ID = $rset_monitor_alarma->ID;
		$CARGO = $rset_monitor_alarma->IDCARGO;
	 }
	 
	 
if($date >= $FECHA && $date <= $FECHAALARMAMAXIMA){
$conn = new XMPPHP_XMPP('192.168.0.180', 5222, 'soaang_admin', 'jdb63jnfd839ohf', 'xmpphp', '192.168.0.180', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
try {
    $conn->use_encryption = False;
    $conn->connect();
    $conn->processUntil('session_start');
    $conn->presence();
    $conn->message($CHAT, 'ALARMA: '.$ATAREA.' ASISTENCIA: '.$ASISTENCIA.' https://'.$ip.'/app/vista/expediente/entrada/expediente_frmexpediente.php?idexped='.$EXPED.'&origen=XMPPHP');
    $conn->disconnect();
    $update_alarma = "update $db1.monitor_tarea set ALARMAENVIADO = 1,FECHAALARMA='".$FECHARECORDATORIO."' where ID = $ID";
    $exec_update_alarma = $con->query($update_alarma); 

} catch(XMPPHP_Exception $e) {
    die($e->getMessage());
}


$sql_cargo_alarma="SELECT AC.IDTAREA,CT.DESCRIPCION,AC.IDCARGO,CU.IDUSUARIO,CU.CHAT FROM catalogo_alarma_cargo AC
INNER JOIN catalogo_usuario CU
ON AC.IDCARGO = CU.IDCARGO INNER JOIN catalogo_tarea CT ON AC.IDTAREA=CT.IDTAREA
WHERE AC.IDCARGO NOT IN(4,".$CARGO.") AND AC.IDTAREA = '".$ATAREA."' ";
 $exec_cargo_alarma = $con->query($sql_cargo_alarma);
	 while($rset_cargo_alarma=$exec_cargo_alarma->fetch_object())
	 {
	 	$XTAREA =$rset_monitor_alarma->DESCRIPCION;
		$XCHAT= $rset_monitor_alarma->CHAT;
	$conn = new XMPPHP_XMPP('192.168.0.180', 5222, 'soaang_admin', 'jdb63jnfd839ohf', 'xmpphp', '192.168.0.180', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
			try {
				$conn->use_encryption = False;
				$conn->connect();
				$conn->processUntil('session_start');
				$conn->presence();
				$conn->message($XCHAT, 'ALARMA: '.$XTAREA.' ASISTENCIA: '.$ASISTENCIA.' https://'.$ip.'/app/vista/expediente/entrada/expediente_frmexpediente.php?idexped='.$EXPED.'&origen=XMPPHP');
				$conn->disconnect(); 
			} catch(XMPPHP_Exception $e) {
				die($e->getMessage());
			}
	}
}

?>
