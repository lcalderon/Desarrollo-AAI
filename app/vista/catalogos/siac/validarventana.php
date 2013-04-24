<?php
 	session_start(); 
	
	include_once('../../../../app/modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/functions.php');
	
	$con= new DB_mysqli();
	 
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);
	
	$respuesta=$con->consultation("SELECT COUNT(*) FROM $con->catalogo.catalogo_afiliado WHERE $ver_cuentas CVEAFILIADO='".$_GET["busqueda"]."' AND IDCUENTA LIKE '%".$_GET["cuenta"]."' AND IDPROGRAMA LIKE '%".$_GET["plan"]."'");

	if($respuesta[0][0]==1 or !$_GET["busqueda"])	header("Location: buscarafiliado.php?busqueda=".$_GET["busqueda"]."&buscarafiliado=1&cuenta=".$_GET["cuenta"]); else header("Location: data_reincidencia.php?busqueda=".$_GET["busqueda"]."&buscarafiliado=1&cuenta=".$_GET["cuenta"]."&plan=".$_GET["plan"]);

?>