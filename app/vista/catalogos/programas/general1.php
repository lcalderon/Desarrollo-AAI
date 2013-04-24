<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/validar_permisos.php');		
	include_once('../../../modelo/clase_mysqli.inc.php');
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
	Auth::required();
	
	$_GET["campo"] = (isset($_GET[campo]))?$_GET[campo]:"IDPROGRAMA";
	$_GET["orden"] = (isset($_GET[orden]))?$_GET[orden]:"ASC";
	$_GET["pag"]= (isset($_GET[pag]))?$_GET[pag]:"1";
 
	if(validar_permisos("CATPLANES_AGREGAR"))	$disabled="";	else $disabled="disabled";
	if(validar_permisos("CATPLANES_EDITAR"))	$disabled=$disabled.",";	else $disabled=$disabled.",disabled";
	if(validar_permisos("CATPLANES_ELIMINAR"))	$disabled=$disabled.",";	else $disabled=$disabled.",disabled";	
	
	$campos="IDPROGRAMA,catalogo_programa.NOMBRE,catalogo_cuenta.NOMBRE,catalogo_programa.ACTIVO ";
	$cabecera=_('ID').","._('NOMBREPLAN').","._('NOMBRECUENTA').","._('STATUS');

	$sql="SELECT catalogo_programa.IDPROGRAMA,catalogo_programa.NOMBRE,catalogo_cuenta.NOMBRE as nombrecuenta ,if(catalogo_programa.ACTIVO='1','ACTIVADO','INACTIVO') as statusplan FROM catalogo_programa inner join catalogo_cuenta on catalogo_cuenta.IDCUENTA=catalogo_programa.IDCUENTA  where catalogo_programa.NOMBRE  like '%".$_POST["busqueda"]."%'  ";

	$rspaginador=$con->query("select if(DATO is null or DATO='',DATODEFAULT,DATO) as numerador from catalogo_parametro where IDPARAMETRO='PAG_CATALOGOS' ");
	$cantidadregistro=$rspaginador->fetch_object();
	$regmostrar=$cantidadregistro->numerador;
	//die($regmostrar);
	$objcat = new Catalogos($sql,$regmostrar,"PLAN");
	
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
			$objcat->MostrarTitulo("CATALOGO DE PLANES");
			$objcat->MostrarBusqueda();
			$objcat->CrearTablaCatalogo($campos,$cabecera,true,$disabled,"catalogos_plan","100%");
		?>
		</div>		
	</body>
</html>