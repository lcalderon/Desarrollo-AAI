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
		
	foreach ($_POST["cbkservicios"] as $grupo){
	
		$valor=$valor.$grupo.",";
	}
	
	$valor=substr($valor,0,strlen($valor)-1);
	
	if($valor >=0){
	
		$rows["IDSERVICIOS"]=$valor;
		$con->update("$con->temporal.monitoreo_servicio_programado_xusuario",$rows,"WHERE TIPO='HOGAR' AND IDCOORDINADOR='".$_SESSION["user"]."'");
	
	}


?> 