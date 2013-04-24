<?php
 	session_start(); 
	
	include_once('../../../../app/modelo/clase_mysqli.inc.php');
	include_once('../../../../app/modelo/functions.php');

	$con= new DB_mysqli();
	 
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
		$row["ACTIVO"]=$_POST["valor"];
		$respuesta=$con->update("$con->catalogo.catalogo_afiliado_persona_vehiculo",$row,"where ID='".$_POST['id']."' ");	
		
		$row2["ID"]=$_POST["id"];
		$row2["IDAFILIADO"]=$_POST["idcodigo"];
		$row2["ACTIVO"]=$_POST["valor"];		
		$row2["IDUSUARIOMOD"]=$_SESSION["user"];		
		if($respuesta)	$con->insert_reg("$con->catalogo.catalogo_afiliado_persona_vehiculo_log",$row2);
		
		if($_POST["valor"]==1)	echo _("ACTIVO"); else echo _("INACTIVO");
?>