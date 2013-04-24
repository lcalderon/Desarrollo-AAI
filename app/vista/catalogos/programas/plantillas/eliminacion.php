<?php

	include_once('../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();
	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	$con->select_db($con->catalogo);
	
	//Eliminar registro

	$respuesta=$con->query("DELETE FROM catalogo_plantillaperfil where IDPLANTILLAPERFIL='".$_GET['codigo']."' ");
	
	echo "<script>";	
   	if(!$respuesta)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
	echo "document.location.href='general.php'";
    echo "</script>";

?>