<?php

	session_start();
	
	include_once("../../../modelo/clase_mysqli.inc.php");
	$con = new DB_mysqli();	
	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	 
	$respuesta=$con->consultation("SELECT COUNT(*)	FROM $con->temporal.asistencia WHERE IDEXPEDIENTE='".$_POST["idexpediente"]."' AND ARRSTATUSASISTENCIA='PRO'");	
	echo $respuesta[0][0];
?>
