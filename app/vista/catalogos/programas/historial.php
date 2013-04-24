<?php

	include_once('../../../modelo/clase_mysqli.inc.php');
		
	$con = new DB_mysqli();
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	session_start();
	
	$Sql="SELECT
		  if(catalogo_programa_log.ACTIVO=1,'ACTIVO','INACTIVO') as status,
		  catalogo_programa_log.PILOTO,
		  catalogo_programa_log.NOMBRE    AS programa,
		  catalogo_cuenta.NOMBRE          AS cuenta,
		  catalogo_programa_log.FECHAINIVIGENCIA,
		  catalogo_programa_log.FECHAFINVIGENCIA,
		  catalogo_programa_log.FECHAMOD,
		  catalogo_programa_log.IDUSUARIOMOD
		FROM $con->catalogo.catalogo_programa_log
		  INNER JOIN $con->catalogo.catalogo_cuenta
			ON catalogo_cuenta.IDCUENTA = catalogo_programa_log.IDCUENTA
		WHERE catalogo_programa_log.IDPROGRAMA = '".$_GET["idprograma"]."'
		ORDER BY catalogo_programa_log.FECHAMOD DESC";
	
	$result=$con->query($Sql);
?>
<HTML>
	<head>
		<title>American Assist</title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<table border="0" cellpadding="1" cellspacing="1" class="catalogos" width="100%">
			<tr bgcolor="#336699">
				<td colspan="9"><font size="2px" color="#FFFFFF"><?=_("HISTORIAL DE CAMBIOS - CATALOGO DE PLANES");?></font></td></tr>
			<tr bgcolor="#000033">
				<td><font color="#FFFFFF"><?=_("FECHAMODIFICA");?></font></td>
				<td><font color="#FFFFFF"><?=_("USUARIO");?></font></td>
				<td><font color="#FFFFFF"><?=_("PROGRAMA");?></font></td>
				<td><font color="#FFFFFF"><?=_("CUENTA");?></font></td>
				<td><font color="#FFFFFF"><?=_("PILOTO");?></font></td>
				<td><font color="#FFFFFF"><?=_("STATUS");?></font></td>
				<td><font color="#FFFFFF"><?=_("FECHAINIVIG.");?></font></td>
				<td><font color="#FFFFFF"><?=_("FECHAFINVIG.");?></font></td>
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
				<td><?=$reg->programa; ?></td>
				<td><?=$reg->cuenta; ?></td>
				<td><?=$reg->PILOTO; ?></td>
				<td><?=$reg->status; ?></td>
				<td><?=$reg->FECHAINIVIGENCIA; ?></td>
				<td><?=$reg->FECHAFINVIGENCIA; ?></td>
			</tr>
		<?php
				$i=$i+1;
			 }
		?>
		</table>
	</BODY>
</HTML>