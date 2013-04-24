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
	
	$con->query("DELETE FROM catalogo_afiliado_persona_vehiculo where ID='".$_GET['idvehiculo']."'");
		
	echo "<script>";
    echo "document.location.href='vehicular_dafiliado.php?idafiliado=".$_GET['idafiliado']."'";
    echo "</script>";

?>