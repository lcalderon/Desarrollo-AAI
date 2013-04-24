<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
		
	$con = new DB_mysqli();
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$con->select_db($con->catalogo);

	session_start();
	
//consulta log
 
	$result=$con->query("SELECT * from  $con->catalogo.catalogo_costo_log where IDCOSTO='".$_GET["idcosto"]."' ORDER BY FECHAMOD DESC");
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
				<td colspan="6"><font size="2px" color="#FFFFFF"><?=_("HISTORIAL DE CAMBIOS - CATALOGO DE COSTOS");?></font></td></tr>
			<tr bgcolor="#000033">
				<td><font color="#FFFFFF"><?=_("FECHAMODIFICA");?></font></td>
				<td><font color="#FFFFFF"><?=_("USUARIO");?></font></td>
				<td><font color="#FFFFFF"><?=_("DESCRIPCION");?></font></td>
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
				<td><?=$reg->IDUSUARIOMOD; ?></td>
				<td><?=$reg->DESCRIPCION; ?></td>
				<td><?=($reg->ACTIVO ==1)?"ACTIVO":"INACTIVO"; ?></td>
			</tr>
		<?php
				$i=$i+1;
			 }
		?>
		</table>
	</body>
</HTML>