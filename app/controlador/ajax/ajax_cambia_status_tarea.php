<?

include_once('../../modelo/clase_mysqli.inc.php');
$con = new  DB_mysqli();

$sql="update 
		$con->temporal.monitor_tarea 
	  set STATUSTAREA='ATENDIDA' 
	  WHERE 
	  	IDASISTENCIA='$_POST[IDASISTENCIA]' 
	  	AND STATUSTAREA='PENDIENTE'  
	  	
	  	";

$con->query($sql);



?>