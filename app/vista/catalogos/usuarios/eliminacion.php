<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");		
	
	$con = new DB_mysqli();
	
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	session_start(); 
	Auth::required();
	
	$con->select_db($con->catalogo);
	
	//Eliminar registro

	$respuesta=$con->query("DELETE FROM catalogo_usuario where IDUSUARIO='".$_GET['codigo']."' ");
	
	echo "<script>";	
   	if(!$respuesta) 	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";		 
	echo "document.location.href='general.php'";
    echo "</script>";

?>