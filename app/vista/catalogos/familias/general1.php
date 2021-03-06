<?php
 //ECHO
	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/validar_permisos.php');		
	include_once("../../../vista/login/Auth.class.php");
	include_once("../Catalogos.class.php");
		
	$con = new DB_mysqli();
	
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	 $con->select_db($con->catalogo);	
		
 	session_start(); 
	Auth::required($_SERVER['REQUEST_URI']);



	$_GET["campo"] = (isset($_GET[campo]))?$_GET[campo]:"IDFAMILIA";
	$_GET["orden"] = (isset($_GET[orden]))?$_GET[orden]:"ASC";
	$_GET["pag"]= (isset($_GET[pag]))?$_GET[pag]:"1";
 
	if(validar_permisos("CATFAMILIAS_AGREGAR"))	$disabled="";	else $disabled="disabled";
	if(validar_permisos("CATFAMILIAS_EDITAR"))	$disabled=$disabled.",";	else $disabled=$disabled.",disabled";
	if(validar_permisos("CATFAMILIAS_ELIMINAR"))	$disabled=$disabled.",";	else $disabled=$disabled.",disabled";	

	$campos="IDFAMILIA,DESCRIPCION,ACTIVO";
	$cabecera=_('ID').","._('DESCRIPCION').","._('STATUS');

	$sql="select IDFAMILIA,DESCRIPCION,if(ACTIVO=1,'ACTIVO','INACTIVO') from catalogo_familia where DESCRIPCION like '%".$_POST["busqueda"]."%'  ";

	$rspaginador=$con->query("select if(DATO is null or DATO='',DATODEFAULT,DATO) as numerador from catalogo_parametro where IDPARAMETRO='PAG_CATALOGOS' ");
	$cantidadregistro=$rspaginador->fetch_object();
	$regmostrar=$cantidadregistro->numerador;
	
	$objcat = new Catalogos($sql,($regmostrar)*1,_('FAMILIA'));
	
?>
<html>
	<head><title>American Assist</title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<script type="text/javascript" src="../../../../estilos/functionjs/ajax_catalogo.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
</head>
	<body onload="document.getElementById('busqueda').focus();">
		<div id="resultado">	
		<?
			$objcat->MostrarTitulo(_('CATALOGO DE FAMILIA'));
			$objcat->MostrarBusqueda();
			$objcat->CrearTablaCatalogo($campos,$cabecera,true,$disabled);
		?>
		</div>		
	</body>
</html>