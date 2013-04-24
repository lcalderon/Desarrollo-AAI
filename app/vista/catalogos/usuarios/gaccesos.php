<?php
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
		
	$con = new DB_mysqli();
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	session_start();
	
 	Auth::required($_POST['txturl']);
	
	$respuesta="1";
 
	$con->query("delete from $con->temporal.seguridad_modulosxusuario where IDUSUARIO='".$_POST["usuario"]."'"); 
	$con->query("delete from $con->temporal.seguridad_acceso_cuenta where IDUSUARIO='".$_POST["usuario"]."'"); 

	foreach ($_POST['ckbpermisos'] as $permiso){
		
		$row["IDMODULO"]=$permiso;
		$row["IDUSUARIO"]=$_POST["usuario"];
		$row["APLICATIVO"]=_("SOAANG");
		$arraynombre = split('_',$permiso);
	
		if($_POST['ckbpermisos']!="")
		 {

		
			$respuesta=$con->insert_reg("$con->temporal.seguridad_modulosxusuario",$row);
			
			$rowmenu["IDMODULO"]="MENU_".$arraynombre[0];
			$rowmenu["IDUSUARIO"]=$_POST["usuario"];
			$rowmenu["APLICATIVO"]=_("SOAANG");
			
			$con->insert_reg("$con->temporal.seguridad_modulosxusuario",$rowmenu);	
			
		 }
	}
	
	foreach ($_POST["chosen"] as $accion){
		$rowacc["IDMODULO"]=$accion;
		$rowacc["IDUSUARIO"]=$_POST["usuario"];
		$rowacc["APLICATIVO"]=_("SOAANG");
		if($_POST['chosen']!="")	$respuesta=$con->insert_reg("$con->temporal.seguridad_modulosxusuario",$rowacc);
	}
	
	if($_POST["rdseleccion"]==1)
	 {
		
		$rows["TODOCUENTAS"]=1;		
		$con->update("$con->catalogo.catalogo_usuario",$rows,"WHERE IDUSUARIO='".$_POST['usuario']."'");
	 
	 }
	else
	 {		
		foreach ($_POST["chkcuentas"] as $cuenta){
			$rowcue["IDCUENTA"]=$cuenta;
			$rowcue["IDUSUARIO"]=$_POST["usuario"];
			$rowcue["IDPAIS"]=$_POST["cmbpais"];
			if($_POST['chkcuentas']!="")	$con->insert_reg("$con->temporal.seguridad_acceso_cuenta",$rowcue);			
		}
			
		$rows["TODOCUENTAS"]=0;		
		$con->update("$con->catalogo.catalogo_usuario",$rows,"WHERE IDUSUARIO='".$_POST['usuario']."'");				
	 }
			
	if($_POST["cmbperfil"]!="")
	 {
		$con->query("insert into $con->temporal.seguridad_modulosxusuario(IDMODULO,IDUSUARIO,APLICATIVO) SELECT IDMODULO,'".$_POST["usuario"]."', '"._("SOAANG")."' AS SOAANG from $con->temporal.acceso_plantillaperfil where  MARCA='' and IDPLANTILLAPERFIL='".$_POST["cmbperfil"]."'");
		$con->query("insert into $con->temporal.seguridad_acceso_cuenta(IDCUENTA,IDUSUARIO,IDPAIS) SELECT IDMODULO,'".$_POST["usuario"]."',IDPAIS from $con->temporal.acceso_plantillaperfil where MARCA!='' and IDPLANTILLAPERFIL='".$_POST["cmbperfil"]."'");
		
		$data=$con->consultation("SELECT COUNT(*) FROM $con->temporal.acceso_plantillaperfil WHERE IDPLANTILLAPERFIL='".$_POST["cmbperfil"]."' AND IDMODULO='ALLCUENTA' ");
		if($data[0][0] ==1)	$con->query("UPDATE $con->catalogo.catalogo_usuario SET TODOCUENTAS=1 WHERE IDUSUARIO='".$_POST['usuario']."'");
	 }
	
	echo "<script>";
	if(!$respuesta)	echo "alert('"._("HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION")."');";
    if($_POST["cmbperfil"]!="")	echo "document.location.href='copiarperfil.php?idusuario=".$_POST["usuario"]."'"; else echo "document.location.href='frmaccesos.php?idusuario=".$_POST["usuario"]."'";
    echo "</script>";
	
?>