<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once("../../../vista/login/Auth.class.php");
	include_once('../../../modelo/functions.php');	
	include_once("../../includes/arreglos.php");
	
	$con= new DB_mysqli();
	 
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
 	session_start(); 
 
 	Auth::required();	
	
	list($allcuentas,$ver_cuentas)=accesos_cuentas($_SESSION["user"]);
	
	$Sql_his="SELECT
			  expediente.IDEXPEDIENTE,
			  expediente.IDAFILIADO,
			  catalogo_programa.NOMBRE       AS programa,
			  catalogo_cuenta.NOMBRE         AS cuenta,
			  CONCAT(expediente_persona.APPATERNO,' ',expediente_persona.APMATERNO,', ', expediente_persona.NOMBRE) AS nombres,
			  expediente.ARRSTATUSEXPEDIENTE,
			  expediente_usuario.FECHAHORA,
			  expediente.IDCUENTA,
			  expediente_usuario.IDUSUARIO
			FROM $con->temporal.expediente
			  INNER JOIN $con->temporal.expediente_persona
				ON expediente_persona.IDEXPEDIENTE = expediente.IDEXPEDIENTE
			  INNER JOIN $con->temporal.expediente_usuario
				ON expediente_usuario.IDEXPEDIENTE = expediente.IDEXPEDIENTE				
			  INNER JOIN $con->catalogo.catalogo_programa
				ON catalogo_programa.IDPROGRAMA = expediente.IDPROGRAMA
			  INNER JOIN $con->catalogo.catalogo_cuenta
				ON catalogo_cuenta.IDCUENTA = expediente.IDCUENTA 			
			WHERE $ver_cuentas
			  expediente_persona.ARRTIPOPERSONA='TITULAR' 
			  AND expediente.CVEAFILIADO='".$_GET["busqueda"]."' 
			  AND expediente.CVEAFILIADO!='' 
			  AND expediente.IDCUENTA LIKE '%".$_GET["cuenta"]."'
			  AND expediente.IDPROGRAMA LIKE '%".$_GET["plan"]."'
			  AND expediente_usuario.ARRTIPOMOVEXP = 'REG'
			  GROUP BY expediente.IDEXPEDIENTE
			  ORDER BY expediente.IDEXPEDIENTE DESC";

	$result=$con->query($Sql_his); 	
	$num=$result->num_rows*1;
	
	if($num==0)	die("*** NO EXISTE INFOMACION EN LAS REINCIDENCIAS HISTORICAS RESPECTO A LA INFORMACION ENVIADA Y A SUS ACCESOS ACTUALES.");
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>American Assist</title>
<style type="text/css">
<!--
.style3 {color: #4E4E4E; font-weight: bold; }
-->
<!--
body {
	background-color: #FBFBFB; 
}
-->
</style>
	<script type="text/javascript" src="../../../../estilos/functionjs/permisos.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
 	
    <style type="text/css">
<!--
.style4 {
	color: #FFFFFF;
	font-weight: bold;
}
.style5 {font-weight: bold}
-->
    </style>
</head>
<body>
<h2 class="Box"><?=_("REINCIDENCIA DE EXPEDIENTE") ;?></h2>
<?
			
	if($result->num_rows*1 > 0)
	 {
?>			 
	<table width="100%" border="0" cellpadding="1" cellspacing="1" style="border:1px solid #E0E0E0">
		<tr>
			<td width="9%" bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("#EXPEDIENTE") ;?></span></div></td>
			<td width="13%" bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("NOMBRE TITULAR") ;?></span></div></td>
			<td width="11%" bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("STATUS") ;?></span></div></td>
			<td width="13%" bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("FECHA REGISTRO") ;?></span></div></td>
			<td width="9%" bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("USUARIO") ;?></span></div></td>
			<td width="11%" bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("CUENTA") ;?></span></div></td>
			<td width="11%" bgcolor="#F2F2F2"><div align="center"><span class="style3"><?=_("PROGRAMA") ;?></span></div></td>
			<td width="11%" bgcolor="#F2F2F2"><div align="center"><span class="style3"></span></div></td>
		</tr>	
		 <?			 
				while($reg = $result->fetch_object())
				 {		 	 
					if($c%2==0) $fondo='#E2E2E2'; else $fondo='#F9F9F9';	
		?>		
			<tr bgcolor="<?=$fondo;?>" >
				<td bgcolor="#E0E0E0"><div align="center"><strong><?=$reg->IDEXPEDIENTE;?></strong></div></td>
				<td><?=$reg->nombres;?></td>			 
				<td align="center"><?=$desc_status_expeduiente[$reg->ARRSTATUSEXPEDIENTE];?></td>
				<td align="center"><?=$reg->FECHAHORA;?></td> 
				<td align="center"><?=$reg->IDUSUARIO;?></td>
				<td><?=$reg->cuenta;?></td> 
				<td><?=$reg->programa;?></td> 
				<td align="center"><input type="button" name="btnasignar" id="btnasignar" value="<?=_("ASIGNAR") ;?>" style="font-weight:bold;font-size:9px;" onClick="window.close();window.opener.recargar_reincidencias('<?=$reg->IDEXPEDIENTE;?>','<?=$reg->IDAFILIADO;?>','<?=$reg->IDCUENTA;?>','<?=$reg->IDPROGRAMA;?>')" /></td> 
			</tr>	
	<?			 
					$c=$c+1;
				 }
	?>
  </table>
	<?	 
		 }
	?>  
  <br>    
</body>
</html>