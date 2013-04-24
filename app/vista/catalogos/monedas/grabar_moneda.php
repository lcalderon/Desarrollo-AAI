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
	
	$rows["IDMONEDA"]=$_POST["idmoneda"];
	$rows["DESCRIPCION"]=$_POST["txtnombre"];
	$rows["SIMBOLO"]=$_POST["txtsimbolo"];
	$rows["ACTIVO"]=$_POST['chkactivo'];
	$rows["IDUSUARIOMOD"]=$_SESSION['user'];

	//Inserta los datos

	$respuesta=$con->insert_reg("$con->catalogo.catalogo_moneda",$rows);

	if($respuesta and $_POST['idmoneda'])	$con->query("INSERT IGNORE INTO $con->catalogo.catalogo_moneda_log SELECT * FROM $con->catalogo.catalogo_moneda WHERE IDMONEDA='".strtoupper($_POST["idmoneda"])."'");
	 
  
	echo "<script>";
	if(!$respuesta)		echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
	echo "document.location.href='general.php' ";
    echo "</script>";	
?>