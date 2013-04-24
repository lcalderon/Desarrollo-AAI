<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
		
	$con = new DB_mysqli();
	
	$con->select_db($con->catalogo);
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	session_start(); 

//consulta sociedad log
	//$result=$con->query("SELECT  FECHAMOD,NOMBRES,APELLIDOS,ACTIVO,EMAIL,BLOQUEADO,VERCUENTAS,if(ACTIVO=1,'ACTIVO','INACTIVO') as statususu,if(REINICIACONTRASENIA=1,'SI','NO') as cambiocontra FROM catalogo_usuario_log WHERE IDUSUARIO='".$_GET["idusuario"]."' ORDER BY FECHAMOD DESC ");
?>
<HTML>
	<head>
		<title>American Assist</title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
	</head>
	<body onKeyDown="javascript:test(event);">
		<table border="0" cellpadding="1" cellspacing="1" class="catalogos">
			<tr bgcolor="#336699">
				<td colspan="6"><font size="2px" color="#FFFFFF"><?=_("HISTORIAL DE CAMBIOS - CATALOGO DE GRUPOS");?></font></td></tr>
			<tr bgcolor="#000033">
				<td><font color="#FFFFFF"><?=_("FECHAMODIFICA");?></font></td>
				<td><font color="#FFFFFF"><?=_("NOMBRES");?></font></td>
				<td><font color="#FFFFFF"><?=_("APELLIDOS");?></font></td>
				<td><font color="#FFFFFF"><?=_("EMAIL");?></font></td>
				<td><font color="#FFFFFF"><?=_("CAMBIA CONTRAS.");?></font></td>
				<td><font color="#FFFFFF"><?=_("STATUS");?></font></td>
			</tr>
		<?php
			// $i=0;
			// while($reg= $result->fetch_object())
			 // {
				 if($i%2==0) $fondo='#f0f0f0'; else $fondo='#bbe0ff';
		?>			
			<tr  bgcolor="#FFFFDF" onMouseOver="this.style.backgroundColor='#e2ebef'; this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='#FFFFDF'">
				<td><b><?=$reg->FECHAMOD; ?></b></td>
				<td><b><?=$reg->NOMBRES; ?></b></td>
				<td><?=$reg->APELLIDOS; ?></td>
				<td><?=$reg->EMAIL; ?></td>
				<td><?=$reg->cambiocontra; ?></td>
				<td><?=$reg->statususu; ?></td>
			</tr>
		<?php
				// $i=$i+1;
			 // }
		?>
		</table>
	</body>
</HTML>