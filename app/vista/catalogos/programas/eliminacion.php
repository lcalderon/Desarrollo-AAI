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
	
	$con->select_db($con->catalogo);	
	
	//Eliminar registro
	
	$datoexp=$con->consultation("SELECT COUNT(*) FROM $con->temporal.expediente WHERE IDPROGRAMA='".$_GET['codigo']."'");
	$datoasit=$con->consultation("SELECT COUNT(*) FROM $con->temporal.asistencia WHERE IDPROGRAMA='".$_GET['codigo']."'");
	
	if($datoexp[0][0]==0 and $datoasit[0][0]==0)
	 {
		$respuesta=$con->query("DELETE FROM catalogo_programa where IDPROGRAMA='".$_GET['codigo']."'");
		if($respuesta)
		 {
			//$con->query("DELETE FROM catalogo_programa_beneficiario where IDPROGRAMA='".$_GET['codigo']."'");
			//$con->query("DELETE FROM catalogo_programa_servicio_beneficiario where IDPROGRAMA='".$_GET['codigo']."'");
			$con->query("DELETE FROM catalogo_programa_servicio where IDPROGRAMA='".$_GET['codigo']."'");
			//$con->query("DELETE FROM catalogo_programa_conformidad where IDPROGRAMA='".$_GET['codigo']."'");
		 }
	 }
	 
	echo "<script>";
	if(!$respuesta)	echo "alert('*** NO SE PUEDE ELIMINAR EL REGISTRO, YA EXISTE ASISTENCIAS O EXPEDIENTES RELACIONADOS A DICHO PLAN.');";
    echo "document.location.href='general.php'";
    echo "</script>";

?>