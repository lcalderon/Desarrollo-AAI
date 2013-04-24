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
		  catalogo_servicio_log.DESCRIPCION,
		  catalogo_plantilla.DESCRIPCION         AS plantilla,
		  catalogo_familia.DESCRIPCION           AS familia,
		  catalogo_servicio_log.FECHAFINVIGENCIA,
		  catalogo_servicio_log.FECHAINIVIGENCIA,
		  catalogo_servicio_log.DURACIONESTIMADA,
		  catalogo_servicio_log.FECHAMOD,
		  catalogo_servicio_log.IDUSUARIOMOD
		FROM $con->catalogo.catalogo_servicio_log
		  INNER JOIN $con->catalogo.catalogo_plantilla
			ON catalogo_plantilla.IDPLANTILLA = catalogo_servicio_log.IDPLANTILLA
		  INNER JOIN $con->catalogo.catalogo_familia
			ON catalogo_familia.IDFAMILIA = catalogo_servicio_log.IDFAMILIA
		WHERE catalogo_servicio_log.IDSERVICIO = '".$_GET["idservicio"]."'
		ORDER BY catalogo_servicio_log.FECHAMOD DESC";
	
//consulta sociedad log
	$result=$con->query($Sql);
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
				<td colspan="8"><font size="2px" color="#FFFFFF"><?=_("HISTORIAL DE CAMBIOS - CATALOGO DE SERVICIOS");?></font></td></tr>
			<tr bgcolor="#000033">
				<td><font color="#FFFFFF"><?=_("FECHAMODIFICA");?></font></td>
				<td><font color="#FFFFFF"><?=_("SERVICIO");?></font></td>
				<td><font color="#FFFFFF"><?=_("PLANTILLA");?></font></td>
				<td><font color="#FFFFFF"><?=_("FAMILIA");?></font></td>
				<td><font color="#FFFFFF"><?=_("FECHAINIVIG.");?></font></td>
				<td><font color="#FFFFFF"><?=_("FECHAFINVIG.");?></font></td>
				<td><font color="#FFFFFF"><?=_("DURACIONESTIM.");?></font></td>
			</tr>
		<?php
			$i=0;
			while($reg= $result->fetch_object())
			 {
				 if($i%2==0) $fondo='#f0f0f0'; else $fondo='#bbe0ff';
		?>			
			<tr  bgcolor="#FFFFDF" onMouseOver="this.style.backgroundColor='#e2ebef'; this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='#FFFFDF'">
				<td><b><?=$reg->FECHAMOD; ?></b></td>
				<td><?=$reg->DESCRIPCION; ?></td>
				<td><?=$reg->plantilla; ?></td>
				<td><?=$reg->familia; ?></td>
				<td><?=$reg->FECHAFINVIGENCIA; ?></td>
				<td><?=$reg->FECHAINIVIGENCIA; ?></td>
				<td><?=$reg->DURACIONESTIMADA; ?></td>
			</tr>
		<?php
				$i=$i+1;
			 }
		?>
		</table>
	</body>
</HTML>