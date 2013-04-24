<?php
 	
	session_start();
	
	include_once('../../../modelo/clase_mysqli.inc.php');

	$con= new DB_mysqli();	
	if ($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
 
 	$rows["AFILIADO_SISTEMA"]=$_POST['status'];
	$resultado=$con->update("$con->catalogo.catalogo_afiliado",$rows,"WHERE IDAFILIADO='".$_POST['idafiliado']."'");

	$rowAFI["IDAFILIADO"]=$_POST['idafiliado'];
	$rowAFI["DESCRIPCION"]=$_POST['status'];
	$rowAFI["TIPOMOVIMIENTO"]="CAMBIO-STATUS_AFILIADO";
	$rowAFI["USUARIOMOD"]=$_SESSION["user"];
	
//Inserta los datos

	if($resultado) $con->insert_reg("catalogo_afiliado_movimiento",$rowAFI);
	
?>