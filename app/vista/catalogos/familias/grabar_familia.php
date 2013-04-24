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

	$rows["IDFAMILIA"]="";
	$rows["DESCRIPCION"]=strtoupper($_POST['txtnombre']);
	$rows["COLOR"]=$_POST['txtcolor'];
	$rows["ACTIVO"]=$_POST['chkactivo'];
	$rows["IDUSUARIOMOD"]=$_SESSION["user"];

//Inserta los datos

	$respuesta=$con->insert_reg('catalogo_familia',$rows);
	$idfam=$con->reg_id();
 
	if($respuesta and $_POST['txtnombre'])	$con->query("INSERT IGNORE INTO $con->catalogo.catalogo_familia_log SELECT * FROM $con->catalogo.catalogo_familia WHERE IDFAMILIA='".$idfam."'");
	
	echo "<script>";
	if(!$respuesta)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
    echo "document.location.href='general.php'";
    echo "</script>";	
?>