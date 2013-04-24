<?php
	session_start();  
	
	include_once("../../modelo/clase_mysqli.inc.php");

	$con = new DB_mysqli();	
 
	if($_POST["marcaOk"] =="on") $rsUpmensaje=$con->query("UPDATE $con->catalogo.catalogo_usuario SET VISUALIZARMENSAJE=0 WHERE IDUSUARIO='".$_SESSION["user"]."' AND VISUALIZARMENSAJE=1");
	header("Location:login.php");		
?>