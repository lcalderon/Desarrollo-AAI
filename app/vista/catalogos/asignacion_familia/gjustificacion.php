<?php
	 
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
			
	$con = new DB_mysqli();
	
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	 $con->select_db($con->catalogo);	
		
 	session_start(); 
 
 
 
	$rows["IDASISTENCIAJUST"]="";
	$rows["IDJUSTIFICACION"]=$_POST['cmbjustificacion'];
	$rows["MOTIVO"]=strtoupper($_POST['txtcomentario']);
	$rows["IDUSUARIOMOD"]=$_SESSION["user"];
	$respuesta=$con->insert_reg("$con->temporal.asistencia_justificacion",$rows);
	
	if($respuesta)	echo _("SE GRABO LA JUSTIFICACION SATISFACTORIAMENTE.");
?>