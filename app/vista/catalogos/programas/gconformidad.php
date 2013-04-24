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

	$verifica=$con->consultation("SELECT count(*) as cantidad from catalogo_programa_conformidad where IDPROGRAMA='".$_GET["programa"]."' and (STATUSCONFIRMA='APROBADO' OR STATUSCONFIRMA='RECHAZO') and CLAVE='".$_GET["codigo"]."'");
	$verifica=$verifica[0][0];	
	
	if($verifica==1)	die("LA URL YA FUE GESTIONADO...");
	
	$verificadata=$con->consultation("SELECT count(*) as cantidad from catalogo_programa_conformidad where IDPROGRAMA='".$_GET["programa"]."' and CLAVE='".$_GET["codigo"]."'");
	$verificadata=$verificadata[0][0];
	
	
	if(!isset($_GET["codigo"]) or !isset($_GET["programa"]) or $verificadata!=1)	die("URL INCORRECTA.");
	
	if($_GET["codigo"]!="" and $_GET["programa"]!="")	$respuesta=$con->query("update catalogo_programa_conformidad set IDUSUARIO='".$_SESSION["user"]."', STATUSCONFIRMA='APROBADO' where CLAVE='".$_GET["codigo"]."' and IDPROGRAMA='".$_GET["programa"]."'");

	$datos=$con->consultation("SELECT count(*) asa cantidad FROM catalogo_programa_conformidad where IDPROGRAMA='".$_GET["programa"]."' and STATUSCONFIRMA='APROBADO' ");
	
	if($dato[0][0]==4)	$con->query("update catalogo_programa set ACTIVO=1 where IDPROGRAMA='".$_GET["programa"]."'");

	if($respuesta)	echo _("SE ACTUALIZO LA CONFORMIDAD PARA EL PROGRAMA."); else echo _("HUBO UN PROBLEMA, NO SE COMPLETO LA OPERACION.");

?>