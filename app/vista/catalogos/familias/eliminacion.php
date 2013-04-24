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
	Auth::required($_GET["urlvalido"]);
	
	$con->select_db($con->catalogo);
	
	$con->query("DELETE FROM catalogo_familia where IDFAMILIA='".$_GET['codigo']."'");
	
	echo "<script>";
    echo "document.location.href='general.php?pag=".$_GET['pag']."'";
    echo "</script>";
?>