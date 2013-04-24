<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('Asignacion.class.php');
		
	$con = new DB_mysqli();

	 
	gettext_simply($lang);
	
	$con->select_db($con->catalogo);
	$temporal=$con->temporal;

	$_GET["campo"] = (isset($_GET[campo]))?$_GET[campo]:"IDASISTENCIA";
	$_GET["orden"] = (isset($_GET[orden]))?$_GET[orden]:"ASC";
	$_GET["pag"]= (isset($_GET[pag]))?$_GET[pag]:"1";
 
	$campos="NOMBRECOMERCIAL,IDASISTENCIA,STATUSPROVEEDOR,TEAT,TEAM";
	$cabecera=_('NOMBRE COMERCIAL').","._('ASISTENCIA').","._('STATUS PROVEEDOR').","._('TEAT').","._('TEAM').","._('MOVIMIENTO');

	$sql="SELECT P.NOMBRECOMERCIAL,PS.IDASISTENCIA,IF(PS.STATUSPROVEEDOR='AC','ACTIVO','CANCELADO'),
	      PS.TEAT,PS.TEAM FROM $temporal.asistencia_asig_proveedor PS
	INNER JOIN catalogo_proveedor P ON PS.IDPROVEEDOR = P.IDPROVEEDOR  and PS.IDASISTENCIA  = '".$_GET["idasist"]."'";

	//echo $sql;
	$rspaginador=$con->query("select if(DATO is null or DATO='',DATODEFAULT,DATO) as numerador from catalogo_parametro where IDPARAMETRO='PAG_CATALOGOS' ");
	$cantidadregistro=$rspaginador->fetch_object();
	$regmostrar=$cantidadregistro->numerador;
	
	$objcat = new Asignacion($sql,$regmostrar,_('PROVEEDOR'));
	
?>
<html>
	<head><title>American Assist</title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
 		
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
</head>
	<body>
		<div id="resultado">	
		<?
			$objcat->MostrarTitulo(_('PROVEEDORES ASIGNADOS'));
			//$objcat->MostrarBusqueda();
			$objcat->CrearTablaCatalogo($campos,$cabecera,false,$origen='mt');
		?>
		</div>		
	</body>
</html>