<?php

	include_once('../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();
	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	$con->select_db($con->catalogo);
	
	session_start();	

	$rows["NOMBRE"]=strtoupper($_POST["txtnombre"]);
	$rows["ACTIVO"]=$_POST['chkactivo'];
	$rows["IDUSUARIOMOD"]=$_SESSION["user"];
	$rows["FECHAMOD"]="";


//Update datos

	$respuesta=$con->update("catalogo_plantillaperfil",$rows,"WHERE IDPLANTILLAPERFIL='".$_POST["codigo"]."'");
 
  
	echo "<script>";
	if(!$respuesta)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
    echo "document.location.href='general.php'";
    echo "</script>";
	
?>