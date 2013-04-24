<?php
	session_start(); 
 
	include_once("../../../../modelo/clase_lang.inc.php");
	include_once("../../../../modelo/clase_mysqli.inc.php");
	include_once("../../../../vista/login/Auth.class.php"); 
	
	$con= new DB_mysqli();
	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	
	if($_POST["idcheck"] =="true"){
	
		$rows["IDCOORDINADOR"]=$_POST["nombrecoord"];
		$rows["TIPO"]="COORD";	
		$respuesta=$con->insert_reg("$con->temporal.monitoreo_servicio_programado",$rows);
	
	} else{
		
		$con->query("DELETE FROM $con->temporal.monitoreo_servicio_programado WHERE IDCOORDINADOR='".$_POST["nombrecoord"]."' AND TIPO='COORD'");
	}
	

?> 