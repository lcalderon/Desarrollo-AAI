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
	
		$rows["IDPROVEEDOR"]=$_POST["idprov"];
		$rows["TIPO"]="AMBULANCIA";	
		$respuesta=$con->insert_reg("$con->temporal.monitoreo_servicio_programado",$rows);
	
	} else{
		
		$con->query("DELETE FROM $con->temporal.monitoreo_servicio_programado WHERE IDPROVEEDOR='".$_POST["idprov"]."' AND TIPO='AMBULANCIA'");
	}
	

?> 