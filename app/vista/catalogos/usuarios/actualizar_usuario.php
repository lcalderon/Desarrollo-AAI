<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");	
		
	$con = new DB_mysqli();
	
	$con->select_db($con->catalogo);
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	session_start();
	Auth::required($_POST["txturl"]);	

	$respuestaeli=$con->query("DELETE FROM $con->temporal.grupo_usuario where IDUSUARIO='".strtoupper($_POST["idusuario"])."'");
	
	$result=$con->query("SELECT CONTRASENIA FROM $con->catalogo.catalogo_usuario where IDUSUARIO='".strtoupper($_POST["idusuario"])."'");
	$row = $result->fetch_object();
	
	$rows["NOMBRES"]=strtoupper($_POST["txtnombre"]);
	$rows["APELLIDOS"]=strtoupper($_POST["txtapellido"]);
	if($row->CONTRASENIA!= $_POST["txtcontrasena"])	$rows["CONTRASENIA"]=sha1(strtoupper($_POST["txtcontrasena"]));
	$rows["REINICIACONTRASENIA"]=$_POST['chkcambia'];
	$rows["ACTIVO"]=$_POST['chkactivo'];
	$rows["EMAIL"]=$_POST['txtemail'];
	$rows["IDUSUARIOMOD"]=$_SESSION["user"];
	$rows["IDCARGO"]=$_POST["cmbcargo"];
	
//Update datos

	$respuesta=$con->update("$con->catalogo.catalogo_usuario",$rows,"WHERE IDUSUARIO='".$_POST['idusuario']."'");

	if($_POST['idusuario'])	$con->query("INSERT IGNORE INTO $con->catalogo.catalogo_usuario_log SELECT * FROM $con->catalogo.catalogo_usuario WHERE IDUSUARIO='".$_POST['idusuario']."'");
	
	
	//Insertar grupos
	if($respuesta and $respuestaeli)
	 {
		foreach ($_POST["cmbgrupos"] as $grupo){
		
			$rowacc["IDGRUPO"]=$grupo;
			$rowacc["IDUSUARIO"]=$_POST["txtusuario"];
			$rowacc["EMAIL"]=$_POST["txtemail"];
			$con->insert_reg("$con->temporal.grupo_usuario",$rowacc);
		}
	 }
   
	echo "<script>";
	if(!$respuesta)		echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";	
	echo "document.location.href='edit_catalogo.php?codigo=".$_POST['idusuario']."'";
    echo "</script>";
	
?>