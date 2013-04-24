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
	
	$rs_ids=$con->query("SELECT DISTINCT IDSERVICIOS AS IDS FROM $con->temporal.monitoreo_servicio_programado_xusuario WHERE monitoreo_servicio_programado_xusuario.TIPO='HOGAR' AND IDCOORDINADOR='".$_SESSION["user"]."'");
	$row_ids = $rs_ids->fetch_object();
	
	if($_POST["idcheck"] =="true"){
	
		$rows["IDPROVEEDOR"]=$_POST["idprov"];
		$rows["IDCOORDINADOR"]=$_SESSION["user"];	
		$rows["TIPO"]="HOGAR";	
		$rows["IDSERVICIOS"]=$row_ids->IDS;	
		$respuesta=$con->insert_reg("$con->temporal.monitoreo_servicio_programado_xusuario",$rows);
	
	} else{
		
		$con->query("DELETE FROM $con->temporal.monitoreo_servicio_programado_xusuario WHERE IDPROVEEDOR='".$_POST["idprov"]."' AND TIPO='HOGAR'");
	}	

?>