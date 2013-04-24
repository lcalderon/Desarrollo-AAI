<?php
 	session_start(); 
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	
	$con = new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
 
	Auth::required($_POST["txturl"]);
	
	//variables POST
 
	$rows["DESCRIPCION"]=$_POST['txtnombre'];
	$rows["COLOR"]=$_POST['txtcolor'];
	$rows["ACTIVO"]=$_POST['chkstatus'];
	$rows["IDUSUARIOMOD"]=$_SESSION["user"];

	//actualiza los datos
 
	$resultado=$con->update("catalogo_familia",$rows,"WHERE IDFAMILIA='".$_POST['idfamilia']."'");
	
	if($_POST['idfamilia'])	$con->query("INSERT IGNORE INTO $con->catalogo.catalogo_familia_log SELECT * FROM $con->catalogo.catalogo_familia WHERE IDFAMILIA='".$_POST['idfamilia']."'");

	echo "<script>";
	if(!$resultado)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";	 
    echo "document.location.href='general.php'";
    echo "</script>";
	
?>