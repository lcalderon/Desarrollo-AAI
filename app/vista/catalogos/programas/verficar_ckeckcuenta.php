<?
	session_start(); 
	
	include_once('../../../modelo/clase_mysqli.inc.php');
	
	$con = new DB_mysqli();
	
	$result=$con->consultation("SELECT VALIDACIONEXTERNA FROM $con->catalogo.catalogo_cuenta WHERE IDCUENTA ='".$_POST["idcuenta"]."'");
	
	echo $result[0][0];
?>