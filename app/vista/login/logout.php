<?php
	
	include_once('../../modelo/clase_mysqli.inc.php');
	include_once("../../vista/login/ActionUser.class.php");
	include_once("../../modelo/functions.php");
	include("browser.class.php");
	
	$con = new DB_mysqli();
	
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	$con->select_db($con->catalogo);
	
	session_start();
	
	$navegadorInfo=getBrowsers();
	
	list($fechahora,$fecha,$hora)=fechahora();
	
	$rowacc["IDUSUARIO"]=strtoupper($_SESSION["user"]);
	$rowacc["APLICATIVO"]=_("SOAANG");
	$rowacc["FECHAMOD"]=$fechahora;
	$rowacc["IDUSUARIOMOVIMIENTO"]="LOGOUT";
	$rowacc["IP"]=$_SERVER["REMOTE_ADDR"];
	$rowacc["HOSTNAME"]="";
	$rowacc["MACHINEID"]=" ";
	$rowacc["VERSIONAPLICATIVO"]=$con->version;
	$rowacc["NAVEGADOR"]=$navegadorInfo['name'];
	$rowacc["VERSIONAVEGADOR"]=$navegadorInfo['version'];
	$rowacc["EXTENSION"]=$_POST["txtextension"];
	$rowacc["SOPERATIVO"]=getOs();
	
	$con->insert_reg("$con->temporal.seguridad_acceso",$rowacc);	
	
	$action = new ActionUser($_SESSION["user"]);
	$action->logout();
	
?>