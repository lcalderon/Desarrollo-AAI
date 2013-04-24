<?php

	session_start();

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/functions.php');
	include_once("../../../vista/login/Auth.class.php"); 

	$con= new DB_mysqli();
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	
//verificar sesion activa
	Auth::required();	

	$valor=0;
	if($_POST["idprogserv"]){

		$rows["ACTIVO"]=($_POST['valor'] =="true")?1:0;

		$respuesta=$con->update("catalogo_programa_servicio",$rows,"WHERE IDPROGRAMASERVICIO='".$_POST['idprogserv']."'");
		
		if($respuesta)	$valor=1;
	}
	
	echo $valor;
?>
	