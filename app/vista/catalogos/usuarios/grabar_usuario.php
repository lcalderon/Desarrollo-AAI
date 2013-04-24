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
	Auth::required($_POST["txturl"]);	
	
	$rows["IDUSUARIO"]=strtoupper($_POST["txtusuario"]);
	$rows["NOMBRES"]=strtoupper($_POST["txtnombre"]);
	$rows["APELLIDOS"]=strtoupper($_POST["txtapellido"]);
	$rows["CONTRASENIA"]=sha1(strtoupper($_POST["txtcontrasena"]));
	$rows["REINICIACONTRASENIA"]=$_POST['chkcambia'];
	$rows["ACTIVO"]=$_POST['chkactivo'];
	$rows["EMAIL"]=$_POST['txtemail'];
	$rows["IDUSUARIOMOD"]=$_SESSION["user"];
	$rows["BLOQUEADO"]=$_POST["chkbloqueado"];

	//Inserta los datos

	$respuesta=$con->insert_reg("$con->catalogo.catalogo_usuario",$rows);
	
	if($respuesta and $_POST["txtusuario"])	$con->query("INSERT IGNORE INTO $con->catalogo.catalogo_usuario_log SELECT * FROM $con->catalogo.catalogo_usuario WHERE IDUSUARIO='".strtoupper($_POST["txtusuario"])."'");
	
	//Insertar grupos
	if($respuesta)
	 {	
		foreach ($_POST["cmbgrupos"] as $grupo){
		
			$rowacc["IDGRUPO"]=$grupo;
			$rowacc["IDUSUARIO"]=$_POST["txtusuario"];
			$rowacc["EMAIL"]=$_POST["txtemail"];
			$con->insert_reg("$con->temporal.grupo_usuario",$rowacc);
		}
	 }
	
	echo "<script>";
	if(!$respuesta)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
	echo "document.location.href='general.php' ";
    echo "</script>";	
?>