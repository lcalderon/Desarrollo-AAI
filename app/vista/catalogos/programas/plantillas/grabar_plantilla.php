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
	
	$rows["IDPLANTILLAPERFIL"]="";
	$rows["NOMBRE"]=strtoupper($_POST["txtnombre"]);
	$rows["ACTIVO"]=$_POST['chkactivo'];
	$rows["IDUSUARIOMOD"]=$_SESSION["user"];
	$rows["FECHAMOD"]="";

	//Inserta los datos

	$respuesta=$con->insert_reg("catalogo_plantillaperfil",$rows);
		
	 
  
	echo "<script>";
	if(!$respuesta)		echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
	echo "document.location.href='general.php' ";
    echo "</script>";	
?>