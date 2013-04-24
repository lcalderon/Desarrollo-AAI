<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	
	$con = new DB_mysqli();
	
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	$con->select_db($con->catalogo);
	 
	$row["BLOQUEADO"]=$_POST["valor"];
	$con->update("catalogo_usuarios",$row,"WHERE CVEUSUARIO='".$_POST['usuario']."'");
	
	$data=$con->consultation("select BLOQUEADO from catalogo_usuarios where CVEUSUARIO='".$_POST['usuario']."'");
	echo $data[0][0];
	 
?>