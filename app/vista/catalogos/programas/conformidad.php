<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");

	$con = new DB_mysqli();
	
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);

	session_start();
	 //Auth::required();
	
	$verifica=$con->consultation("SELECT count(*) as cantidad from catalogo_programa_conformidad where IDPROGRAMA='".$_GET["programa"]."' and STATUSCONFIRMA='APROBADO' and CLAVE='".$_GET["codigo"]."'");
	$verifica=$verifica[0][0];
	
	if($verifica==1)	die("LA URL YA FUE CONFIRMADO...");

?>
<HTML>
 <HEAD>
  <TITLE>Respuesta</TITLE>
  		
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>		
		<script language="JavaScript">			

			function verificar(){
				if(confirm('<?=_("ESTA SEGURO DE PROSEGUIR CON LA ACTULIZACION DE LA CONFORMIDAD?.") ;?>'))
				{
					 reDirigir('gconformidad.php?programa=<?=$_GET["programa"];?>&codigo=<?=$_GET["codigo"];?>');
				}

			}			
		</script>
 </HEAD>
 <BODY onload="verificar();">  
 </BODY>
</HTML>