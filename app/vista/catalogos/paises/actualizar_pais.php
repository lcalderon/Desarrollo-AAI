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

//agrega pais
 
	$rows["NOMBRE"]=$_POST["txtnombre"];
	//$rows["CVEMONEDA"]=$_POST['cmbmoneda'];
	//$rows["ACTIVO"]=$_POST['chkactivo'];
	$rows["IDUSUARIOMOD"]="";
	$rows["FECHAMOD"]="";


//Update datos

	$respuesta=$con->update("catalogo_pais",$rows,"WHERE IDPAIS='".$_POST["idpais"]."'");
 
  
	echo "<script>";
	if(!$respuesta)
	 {
		echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
	 }
    echo "document.location.href='general.php'";
    echo "</script>";
	
?>