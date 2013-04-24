<?php

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
	
	validar_permisos("MENU_CATUSUARIOS",1);

	$_GET["campo"] = (isset($_GET[campo]))?$_GET[campo]:"IDUSUARIO";
	$_GET["orden"] = (isset($_GET[orden]))?$_GET[orden]:"ASC";
	$_GET["pag"]= (isset($_GET[pag]))?$_GET[pag]:"1";
	
//	if(validar_permisos("CATUSUARIOS_AGREGAR"))	$disabled="";	else $disabled="disabled";
//	if(validar_permisos("CATUSUARIOS_EDITAR"))	$disabled=$disabled.",";	else $disabled=$disabled.",disabled";
//	if(validar_permisos("CATUSUARIOS_ELIMINAR"))	$disabled=$disabled.",";	else $disabled=$disabled.",disabled";		
// 
	$campos="IDUSUARIO,NOMBRES,APELLIDOS,ACTIVO,EMAIL";
	$cabecera=_('ID').","._('NOMBRES').","._('APELLIDOS').","._('STATUS').","._('EMAIL');
	
	$IS_ADM=$con->consultation("SELECT COUNT(*) FROM $con->temporal.grupo_usuario where IDUSUARIO='".$_SESSION["user"]."' AND IDGRUPO='ADMI'");
	if($IS_ADM[0][0] ==0 and $_SESSION["user"]!="ADMINISTRADOR")	$subquery="AND IDUSUARIO !='ADMINISTRADOR'";
	
	$sql="SELECT IDUSUARIO,NOMBRES,APELLIDOS,if(ACTIVO=1,'ACTIVO','INACTIVO'),EMAIL  FROM catalogo_usuario where (IDUSUARIO like '%".$_POST["busqueda"]."%'  or NOMBRES like '%".$_POST["busqueda"]."%'  or APELLIDOS like '%".$_POST["busqueda"]."%') $subquery";

	$rspaginador=$con->query("select if(DATO is null or DATO='',DATODEFAULT,DATO) as numerador from catalogo_parametro where IDPARAMETRO='PAG_CATALOGOS' ");	
	$cantidadregistro=$rspaginador->fetch_object();
	$regmostrar=$cantidadregistro->numerador;
	
	$objcat = new Catalogos($sql,($regmostrar)*1,_('USUARIO'));
 
?>
<html>
	<head><title>American Assist</title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<script type="text/javascript" src="../../../../estilos/functionjs/ajax_catalogo.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	</head>
	<body onload="document.getElementById('busqueda').focus()">
		<div id="resultado">
		<h2><font size='3px'><?=_("CATALOGO DE USUARIOS") ;?></font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size='2px' color="#FF6666"><input type="radio" name="radio" id="radio" value="0" checked>Usuarios&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="radio" id="radio" value="1" onClick="reDirigir('../perfilusuario/general.php')">Plantillas</font>&nbsp;&nbsp;<input type="radio" name="radio" id="radio" value="2" onClick="reDirigir('../grupos/general.php')"><font size='2px' color="#FF6666">Configurar Grupos</font></h2>		
		<?
			$objcat->MostrarBusqueda();
			$objcat->CrearTablaCatalogo($campos,$cabecera,true,$disabled,"catalogos_usuarios","100%");		 
		?>
		</div>		
	</body>
</html>