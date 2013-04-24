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

//consulta sociedad log
	$result=$con->query("select * from  $con->catalogo.catalogo_cuenta_log where IDCUENTA='".$_GET["idcuenta"]."' order by FECHAMOD DESC");
?>
<HTML>
	<head>
		<title>American Assist</title>
		<script language="JavaScript" type="text/javascript" src="ajax_servicio.js"></script>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	</head>
	<body onKeyDown="javascript:test(event);">
		<table border="0" cellpadding="1" cellspacing="1" class="catalogos" width="100%">
			<tr bgcolor="#336699">
				<td colspan="6"><font size="2px" color="#FFFFFF"><?=_("HISTORIAL DE CAMBIOS - CATALOGO DE CUENTAS");?></font></td></tr>
			<tr bgcolor="#000033">
				<td><font color="#FFFFFF"><?=_("FECHAMODIFICA");?></font></td>
				<td><font color="#FFFFFF"><?=_("USUARIO");?></font></td>
				<td><font color="#FFFFFF"><?=_("NOMBRE");?></font></td>
				<td><font color="#FFFFFF"><?=_("AFILIADO");?></font></td>
				<td><font color="#FFFFFF"><?=_("PILOTO");?></font></td>
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
				<td><?=$reg->NOMBRE; ?></td>
				<td><?=($reg->AFILIADOS ==1)?"SI":"NO"; ?></td>
				<td><?=$reg->PILOTO; ?></td>
				<td><?=($reg->ACTIVO ==1)?"ACTIVO":"INACTIVO"; ?></td>
			</tr>
		<?php
				$i=$i+1;
			 }
		?>
		</table>
	</body>
</HTML>