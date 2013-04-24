<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	
	$con = new DB_mysqli();
	
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	session_start(); 
	
	$con->select_db($con->catalogo);
	
	$con->query("DELETE FROM catalogo_afiliado_beneficiario_ubigeo where IDBENEFICIARIO='".$_GET['idbeneficiario']."'");
	$con->query("DELETE FROM catalogo_afiliado_beneficiario_telefono where IDBENEFICIARIO='".$_GET['idbeneficiario']."'");
	$con->query("DELETE FROM catalogo_afiliado_beneficiario where IDBENEFICIARIO='".$_GET['idbeneficiario']."'");
	
	echo "<script>";
    echo "document.location.href='beneficiario.php?idafiliado=".$_GET['idafiliado']."'";
    echo "</script>";

?>