<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");	
	
	$con = new DB_mysqli();
	
 	session_start();	
	Auth::required($_GET["urlvalido"]);
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	             
	//Eliminar registro
	
	$datoexp=$con->consultation("SELECT COUNT(*) FROM $con->temporal.expediente WHERE IDCUENTA='".$_GET['codigo']."'");
	$datoasit=$con->consultation("SELECT COUNT(*) FROM $con->temporal.asistencia WHERE IDCUENTA='".$_GET['codigo']."'");
	
	if($datoexp[0][0]==0 and $datoasit[0][0]==0){
		$respuesta=$con->query("DELETE FROM catalogo_cuenta where IDCUENTA='".$_GET['codigo']."'");
		/* 	if($respuesta)
		 {			
			$con->query("DELETE FROM catalogo_programa where IDCUENTA='".$_GET['codigo']."'");
			$con->query("DELETE FROM catalogo_programa_servicio where IDCUENTA='".$_GET['codigo']."'");
		 } */
	 }
	 
	echo "<script>";
	if(!$respuesta)	echo "alert('*** NO SE PUEDE ELIMINAR EL REGISTRO, YA EXISTE ASISTENCIAS O EXPEDIENTES RELACIONADOS A DICHA CUENTA.');";
    echo "document.location.href='general.php'";
    echo "</script>";

?>