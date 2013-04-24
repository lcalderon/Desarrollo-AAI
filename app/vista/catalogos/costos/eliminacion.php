<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	
	$con = new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
 
 	session_start();	
	Auth::required($_GET["urlvalido"]);

	//Eliminar registro
	
	$con->query("DELETE FROM catalogo_costo where IDCOSTO='".$_GET['codigo']."'");
	
	echo "<script>";
    echo "document.location.href='general.php'";
    echo "</script>";
?>