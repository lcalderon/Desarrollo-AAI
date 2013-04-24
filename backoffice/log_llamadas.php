<?php
$fecha = $argv[1];
//$fecha = '20091203';
//echo $fecha;
include_once('../app/modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
$con->select_db($con->temporal);

$sql="INSERT INTO monitor_llamada_log(UNIQUEID,TELEFONO,CHANNEL,DNID,EXTENSION,FECHA,STATUS)
	SELECT UNIQUEID,TELEFONO,CHANNEL,DNID,EXTENSION,FECHA,STATUS
	FROM monitor_llamada 
	WHERE DATE_FORMAT(FECHA,'%Y%m%d')='$fecha'";
//echo $sql;
$exec_sql = $con->query($sql);

$sql_queue = "INSERT INTO monitor_llamada_queue_log(call_uniqueid,call_schannel,call_suniqueid,call_exten,call_callid,call_fecha,call_contexto,call_estado)
		SELECT call_uniqueid,call_schannel,call_suniqueid,call_exten,call_callid,call_fecha,call_contexto,call_estado from monitor_llamada_queue
		WHERE DATE_FORMAT(call_fecha,'%Y%m%d')='$fecha'";
$exec_sql_queue = $con->query($sql_queue); 
//echo $sql_queue;

$sql_elimina_monitor = "delete from monitor_llamada WHERE DATE_FORMAT(FECHA,'%Y%m%d')='$fecha'";
echo $sql_elimina_monitor;
$exec_elimina_monitor = $con->query($sql_elimina_monitor);

$sql_elimina_monitor_queue = "delete from monitor_llamada_queue WHERE DATE_FORMAT(call_fecha,'%Y%m%d')='$fecha'";
$exec_elimina_monitor_queue = $con->query($sql_elimina_monitor_queue);


?>
