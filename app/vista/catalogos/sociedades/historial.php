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
	$result=$con->query("SELECT FECHAMOD,NOMBRE,IPSERVIDOR_ASTERISK,USUARIO_MANAGER,PREFIJO,CONTEXTO,if(ACTIVO=1,'ACTIVO','INACTIVO') as statuso FROM catalogo_sociedad_log WHERE IDSOCIEDAD='".$_GET["idsociedad"]."' ORDER BY FECHAMOD DESC ");
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
				<td colspan="7"><font size="2px" color="#FFFFFF"><?=_("HISTORIAL DE CAMBIOS - CATALOGO DE FAMILIAS");?></font></td></tr>
			<tr bgcolor="#000033">
				<td><font color="#FFFFFF"><?=_("FECHAMODIFICA");?></font></td>
				<td><font color="#FFFFFF"><?=_("NOMBRE");?></font></td>
				<td><font color="#FFFFFF"><?=_("IP SERV. ASTERISK");?></font></td>
				<td><font color="#FFFFFF"><?=_("PREFIJO");?></font></td>
				<td><font color="#FFFFFF"><?=_("CONTEXO");?></font></td>
				<td><font color="#FFFFFF"><?=_("USUARIO MANAGER");?></font></td>
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
				<td><b><?=$reg->NOMBRE; ?></b></td>
				<td><?=$reg->IPSERVIDOR_ASTERISK; ?></td>
				<td><?=$reg->PREFIJO; ?></td>
				<td><?=$reg->CONTEXTO; ?></td>
				<td><?=$reg->USUARIO_MANAGER; ?></td>
				<td><?=$reg->statuso; ?></td>
			</tr>
		<?php
				$i=$i+1;
			 }
		?>
		</table>
	</body>
</HTML>