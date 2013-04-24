<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
		
	$con = new DB_mysqli();
	
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	 $con->select_db($con->catalogo);	
		
 	session_start();
	
	//Eliminar registro
 
	$respuesta=$con->query("DELETE FROM catalogo_programa where IDPROGRAMA='".$_GET['idprograma']."'");
	if($respuesta)
	 {
		$con->query("DELETE FROM catalogo_programa_beneficiario where IDPROGRAMA='".$_GET['idprograma']."'");
		$con->query("DELETE FROM catalogo_programa_servicio_beneficiario where IDPROGRAMA='".$_GET['idprograma']."'");
		$con->query("DELETE FROM catalogo_programa_servicio where IDPROGRAMA='".$_GET['idprograma']."'");
	 }
	 
	echo "<script>";
    echo "document.location.href='edit_catalogo.php?codigo=".$_GET['codigo']."'";
    echo "</script>";

?>