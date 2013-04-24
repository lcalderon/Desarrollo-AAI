<?php
 	session_start(); 
	include_once("../../modelo/clase_mysqli.inc.php");
	include_once("../../vista/login/Auth.class.php");
	
	$con = new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

	Auth::required($_POST["txturl"]);
	
//variables POST	 

	$rows["IDDEFICIENCIA_CORRELATIVO"]=strtoupper($_POST['txtcodigo']);
	$rows["DESCARGO"]=strtoupper($_POST['txtasegimiento']);
	$rows["IDUSUARIOMOD"]=$_SESSION["user"];
	
//Inserta los datos

	$respuesta=$con->insert_reg("$con->temporal.asistencia_deficiencia_descargo",$rows);

?>