<?php
	
	session_start();
	
	include_once('../../modelo/clase_mysqli.inc.php');
	
	$con = new DB_mysqli();
	
	$con->select_db($con->catalogo);
			
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 	
	$usuario=strtoupper($_POST["usuario"]);
	$passactual=sha1(strtoupper($_POST["passactual"]));
	
	$usu=$con->consultation("select count(IDUSUARIO) as existe from catalogo_usuario where IDUSUARIO='$usuario' and CONTRASENIA='$passactual' ");
	
	echo $usu[0]["existe"];
	//echo "select count(IDUSUARIO) as existe from catalogo_usuario where IDUSUARIO='$usuario' and CONTRASENIA='$passactual' ";

?>
