<?php

	include_once('../../../modelo/clase_mysqli.inc.php');

	$con = new DB_mysqli();	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	//Eliminar registro

	$respuesta=$con->query("DELETE FROM $con->catalogo.catalogo_grupo where IDGRUPO='".$_GET['codigo']."' ");
	
	echo "<script>";	
   	if(!$respuesta)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
	echo "document.location.href='general.php'";
    echo "</script>";

?>