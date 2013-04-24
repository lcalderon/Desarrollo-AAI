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
	
	$rows["IDPAIS"]=$_POST["txtcodigo"];
	$rows["NOMBRE"]=$_POST["txtnombre"];
	$rows["IDUSUARIOMOD"]="";
	$rows["FECHAMOD"]="";

	//Inserta los datos

		$respuesta=$con->insert_reg("catalogo_pais",$rows);
		
	 
  
	echo "<script>";
	if(!$respuesta)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
	echo "document.location.href='general.php' ";
    echo "</script>";	
?>