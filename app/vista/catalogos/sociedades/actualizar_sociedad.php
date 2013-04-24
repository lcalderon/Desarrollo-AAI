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

//agregar
 
	$pass=$con->consultation("SELECT PASSWORD_MANAGER FROM $con->catalogo.catalogo_sociedad where IDSOCIEDAD='".$_POST["idsociedad"]."'");
 
	$rows["NOMBRE"]=strtoupper($_POST["txtnombre"]);
	$rows["IPSERVIDOR_ASTERISK"]=$_POST["txtip"];
	$rows["USUARIO_MANAGER"]=strtoupper($_POST["txtusuariomanag"]);
	if($pass[0][0]!= $_POST["txtpasswordmanag"])	$rows["CONTRASENIA"]=sha1($_POST["txtpasswordmanag"]);
	$rows["PREFIJO"]=strtoupper($_POST["txtprefijo"]);
	$rows["CONTEXTO"]=strtoupper($_POST["txtcontexto"]);	
	$rows["ACTIVO"]=$_POST['chkactivo'];
	$rows["IDUSUARIOMOD"]=$_SESSION['user'];

//Update datos

	$respuesta=$con->update("$con->catalogo.catalogo_sociedad",$rows,"WHERE IDSOCIEDAD='".$_POST["idsociedad"]."'");
 
	//if($respuesta and $_POST["idsociedad"])	$con->query("INSERT IGNORE INTO $con->catalogo.catalogo_sociedad_log SELECT * FROM $con->catalogo.catalogo_sociedad WHERE IDSOCIEDAD='".$_POST["idsociedad"]."'");
 
  
	echo "<script>";
	if(!$respuesta)
	 {
		echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
	 }
    echo "document.location.href='general.php'";
    echo "</script>";
	
?>