<?php

	include_once('../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	session_start();	

	$rows["NOMBRE"]=strtoupper($_POST["txtnombre"]);
	$rows["ACTIVO"]=$_POST['chkactivo'];
	$rows["IDUSUARIOMOD"]=$_SESSION["user"];

//Update datos

	$respuesta=$con->update("$con->catalogo.catalogo_grupo",$rows,"WHERE IDGRUPO='".$_POST["codigo"]."'");
 
  
	echo "<script>";
	if(!$respuesta)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
    echo "document.location.href='edit_catalogo.php?codigo=".$_POST['codigo']."'";
    echo "</script>";
	
?>