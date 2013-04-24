<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/clase_lang.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");	
	
	$con= new DB_mysqli();
	 
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
		
 	session_start(); 
 	Auth::required(); 

		$Sql="SELECT
					  IDAFILIADO,
					  IDUSUARIOMOD,
					  ACTIVO,
					  FECHAMOD 
					FROM $con->catalogo.catalogo_afiliado_persona_vehiculo_log 
					WHERE ID= '".$_GET["id"]."'
					ORDER BY FECHAMOD DESC";
 
		$rs=$con->query($Sql);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>American Assist</title>

	<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	

	 

  
</head>
<body>
<form id="form1" name="form1" method="post" action=""  > 

<table width="100%" border="0" cellpadding="1" cellspacing="2" style="border:1px solid #A6C9E2">
		<tr>
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	     <strong> <?=_("USUARIO") ;?></strong>
		  </span></div></td>		
		 <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	     <strong><?=_("STATUS") ;?></strong>
		  </span></div></td>	
		  <td bgcolor="#E1EFFB"><div align="center"><span class="style3">
	      <strong><?=_("FECHAMODIFICA") ;?></strong>
		  </span></div></td>

		</tr>

		 <?
			while($reg = $rs->fetch_object())
			 {			 	 
		?>		
		<tr bgcolor="#E5E5E5">					
			<td align="center"><?=$reg->IDUSUARIOMOD;?></td>			
			<td align="center"><?=($reg->ACTIVO==1)?_("ACTIVO"):_("INACTIVO")?></td>			
			<td align="center"><?=$reg->FECHAMOD;?></td>			

		 </tr>	
		<?		 
			}
		?>
  </table>

</form>
</body> 
	