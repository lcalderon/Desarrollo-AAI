<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	
	$con = new DB_mysqli();	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	session_start();
	 
	$con->query("delete from $con->temporal.acceso_plantillaperfil where IDPLANTILLAPERFIL='".$_POST["idperfil"]."'"); 
	 
	foreach ($_POST['ckbpermisos'] as $permiso){
	
		$row["IDPLANTILLAPERFIL"]=$_POST["idperfil"];
		$row["IDMODULO"]=$permiso;
		$arraynombre = split('_',$permiso);
		
		if($_POST['ckbpermisos']!="")
		 {			
			$respuesta=$con->insert_reg("$con->temporal.acceso_plantillaperfil",$row);
			
			$rowmenu["IDPLANTILLAPERFIL"]=$_POST["idperfil"];
			$rowmenu["IDMODULO"]="MENU_".$arraynombre[0];
			
			$con->insert_reg("$con->temporal.acceso_plantillaperfil",$rowmenu);	
			
		 }		
	}		
	
	foreach ($_POST["chosen"] as $accion){
	
		$rowacc["IDPLANTILLAPERFIL"]=$_POST["idperfil"];
		$rowacc["IDMODULO"]=$accion;
		if($_POST['chosen']!="")	$respuesta=$con->insert_reg("$con->temporal.acceso_plantillaperfil",$rowacc);	
	}
		
	
	if($_POST["rdseleccion"]==1)
	 {
		$rowacc["IDPLANTILLAPERFIL"]=$_POST["idperfil"];
		$rowacc["IDMODULO"]="ALLCUENTA";
		$rowacc["IDPAIS"]=$_POST["cmbpais"];
		
		$con->insert_reg("$con->temporal.acceso_plantillaperfil",$rowacc);	
	 
	 }
	else
	 {		
		foreach ($_POST["chkcuentas"] as $cuenta){
		
			$rowacc["IDPLANTILLAPERFIL"]=$_POST["idperfil"];
			$rowacc["IDMODULO"]=$cuenta;
			$rowacc["MARCA"]=1;
			if($_POST['chkcuentas']!="")	$con->insert_reg("$con->temporal.acceso_plantillaperfil",$rowacc);	
		
		}
	 }

	 

	echo "<script>";
	if(!$respuesta)		echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
    echo "document.location.href='frmaccesos.php?idperfil=".$_POST["idperfil"]."'";
    echo "</script>";	
	
?>