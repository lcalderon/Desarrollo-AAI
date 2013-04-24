<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");	
	
	$con = new DB_mysqli();
	
	session_start();		
	Auth::required();
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }       
	

	//Eliminar registro
 
	$dato=$con->consultation("SELECT COUNT(*) FROM $con->temporal.asistencia WHERE IDPROGRAMASERVICIO='".$_GET['idprogramaserv']."' AND IDCUENTA='".$_GET['idcuenta']."' AND IDPROGRAMA='".$_GET['idprograma']."'");
 	if($dato[0][0]==0)
	 {
		$respuesta=$con->query("DELETE FROM $con->catalogo.catalogo_programa_servicio where IDPROGRAMASERVICIO='".$_GET['idprogramaserv']."'");
	
	 }
	
	echo "<script>";
	if(!$respuesta)	echo "alert('*** NO SE PUEDE ELIMINAR EL REGISTRO, YA EXISTE ASISTENCIA(S) RELACIONADOS A DICHO SERVICIO.***');";
	echo "document.location.href='edit_catalogo.php?codigo=".$_GET['idprograma']."&opc=".$_GET['opc']."'";
	echo "</script>";

?>