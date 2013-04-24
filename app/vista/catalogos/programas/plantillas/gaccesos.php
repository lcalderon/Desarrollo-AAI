<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	
	$con = new DB_mysqli();
	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	$con->select_db($con->catalogo);

	session_start();

	$respuesta="1";
	 
	 $con->query("delete from catalogo_plantillaperfil_usuario where IDPLANTILLAPERFIL='".$_POST["idperfil"]."'"); 
 
	 
	foreach ($_POST['ckbpermisos'] as $permiso){
	
		$row["IDPLANTILLAPERFIL"]=$_POST["idperfil"];
		$row["IDMODULO"]=$permiso;
		$arraynombre = split('_',$permiso);
		
		if($_POST['ckbpermisos']!="")
		 {			
			$respuesta=$con->insert_reg("catalogo_plantillaperfil_usuario",$row);
			
			$rowmenu["IDPLANTILLAPERFIL"]=$_POST["idperfil"];
			$rowmenu["IDMODULO"]="MENU_".$arraynombre[0];
			
			$con->insert_reg("catalogo_plantillaperfil_usuario",$rowmenu);	
			
		 }		
	}		
	
	foreach ($_POST["chosen"] as $accion){
	
		$rowacc["IDPLANTILLAPERFIL"]=$_POST["idperfil"];
		$rowacc["IDMODULO"]=$accion;
		if($_POST['chosen']!="")	$respuesta=$con->insert_reg("catalogo_plantillaperfil_usuario",$rowacc);	
	}
		
	// foreach ($_POST["chkcuentas"] as $cuenta){
	
		// $rowacc["IDPLANTILLAPERFIL"]=$_POST["idperfil"];
		// $rowacc["IDMODULO"]=$cuenta;
		// $rowacc["IDPAIS"]=$_POST["cmbpais"];
		// if($_POST['chkcuentas']!="")	$con->insert_reg("catalogo_plantillaperfil_usuario",$rowacc);	
	// }
	
	
	
	// if($_POST["rdseleccion"]==1)
	 // {
		
		// $con->query("insert into catalogo_plantillaperfil_usuario(IDPLANTILLAPERFIL,IDMODULO,IDPAIS) SELECT '".$_POST["idperfil"]."' as perfil ,IDCUENTA,'".$_POST["cmbpais"]."'  AS aplicativo from $con->catalogo.catalogo_cuenta");
		// $rowall["IDPLANTILLAPERFIL"]=$_POST["idperfil"];
		// $rowall["IDMODULO"]="VERALL";				
		// $rowall["IDPAIS"]=$_POST["cmbpais"];			
		// $con->insert_reg("catalogo_plantillaperfil_usuario",$rowall);	
	 
	 // }
	// else
	 // {		
		// foreach ($_POST["chkcuentas"] as $cuenta){
			
		// $rowacc["IDPLANTILLAPERFIL"]=$_POST["idperfil"];
		// $rowacc["IDMODULO"]=$cuenta;
		// $rowacc["IDPAIS"]=$_POST["cmbpais"];
		// if($_POST['chkcuentas']!="")	$con->insert_reg("catalogo_plantillaperfil_usuario",$rowacc);	
		
		// }
	 // }

	 

	echo "<script>";
	if(!$respuesta)		echo "alert('HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.');";
    echo "document.location.href='frmaccesos.php?idperfil=".$_POST["idperfil"]."'";
    echo "</script>";	
	
?>