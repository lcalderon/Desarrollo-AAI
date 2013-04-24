<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
		
	$con = new DB_mysqli();	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	
 	session_start();
	Auth::required($_POST["txturl"]);
	
	$con->select_db($con->catalogo);
	
	$rows["IDSOCIEDAD"]=$_POST["txtcodigo"];
	$rows["NOMBRE"]=strtoupper($_POST["txtnombre"]);
	$rows["IPSERVIDOR_ASTERISK"]=$_POST["txtip"];
	$rows["USUARIO_MANAGER"]=strtoupper($_POST["txtusuariomanag"]);
	$rows["PASSWORD_MANAGER"]=sha1($_POST["txtpasswordmanag"]);
	$rows["PREFIJO"]=strtoupper($_POST["txtprefijo"]);
	$rows["CONTEXTO"]=strtoupper($_POST["txtcontexto"]);
	$rows["ACTIVO"]=$_POST['chkactivo'];
	$rows["IDUSUARIOMOD"]=$_SESSION['user'];


	//Inserta los datos

		$respuesta=$con->insert_reg("catalogo_sociedad",$rows);
		if($respuesta and $_POST["txtcodigo"])	$con->query("INSERT IGNORE INTO $con->catalogo.catalogo_sociedad_log SELECT * FROM $con->catalogo.catalogo_sociedad WHERE IDSOCIEDAD='".$_POST["txtcodigo"]."'");
			
	 
  
	echo "<script>";
	if(!$respuesta)	echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
	echo "document.location.href='general.php' ";
    echo "</script>";	
?>