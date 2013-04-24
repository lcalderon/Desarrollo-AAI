<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
		
	$con = new DB_mysqli();
	
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	session_start(); 

//consulta sociedad log
	$result=$con->query("SELECT FECHAMOD,DESCRIPCION,SIMBOLO,if(ACTIVO=1,'ACTIVO','INACTIVO') as statusm FROM $con->catalogo.catalogo_moneda_log WHERE IDMONEDA='".$_GET["idmoneda"]."' ORDER BY FECHAMOD DESC");
?>
<HTML>
	<head>
		<title>American Assist</title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
	</head>
	<body onKeyDown="javascript:test(event);">
		<table border="0" cellpadding="1" cellspacing="1" class="catalogos" width="100%">
			<tr bgcolor="#336699">
				<td colspan="6"><font size="2px" color="#FFFFFF"><?=_("HISTORIAL DE CAMBIOS - CATALOGO DE FAMILIAS");?></font></td></tr>
			<tr bgcolor="#000033">
				<td><font color="#FFFFFF"><?=_("FECHAMODIFICA");?></font></td>
				<td><font color="#FFFFFF"><?=_("DESCRIPCION");?></font></td>
				<td><font color="#FFFFFF"><?=_("NOMBRE");?></font></td>
				<td><font color="#FFFFFF"><?=_("STATUS");?></font></td>
			</tr>
		<?php
			$i=0;
			while($reg= $result->fetch_object())
			 {
				 if($i%2==0) $fondo='#f0f0f0'; else $fondo='#bbe0ff';
		?>			
			<tr  bgcolor="#FFFFDF" onMouseOver="this.style.backgroundColor='#e2ebef'; this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='#FFFFDF'">
				<td><b><?=$reg->FECHAMOD; ?></b></td>
				<td><b><?=$reg->DESCRIPCION; ?></b></td>
				<td><?=$reg->SIMBOLO; ?></td>
				<td><?=$reg->statusm; ?></td>
			</tr>
		<?php
				$i=$i+1;
			 }
		?>
		</table>
	</body>
</HTML>