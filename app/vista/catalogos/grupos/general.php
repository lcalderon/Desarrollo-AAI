<?php

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
	 
	session_start();
  	Auth::required();

	//if(validar_permisos("CATGRUPOS_AGREGAR"))	$disabled="";	else $disabled="disabled";
	//if(validar_permisos("CATGRUPOS_EDITAR"))	$disabled=$disabled.",";	else $disabled=$disabled.",disabled";
	//if(validar_permisos("CATGRUPOS_ELIMINAR"))	$disabled=$disabled.",";	else $disabled=$disabled.",disabled";
	
	$_GET["campo"] = (isset($_GET[campo]))?$_GET[campo]:"IDGRUPO";
	$_GET["orden"] = (isset($_GET[orden]))?$_GET[orden]:"ASC";
	$_GET["pag"]= (isset($_GET[pag]))?$_GET[pag]:"1";
 
	$campos="IDGRUPO,NOMBRE,ACTIVO";
	$cabecera=_('ID').","._('NOMBRE').","._('STATUS');

	$sql="SELECT IDGRUPO,NOMBRE,if(ACTIVO=1,'ACTIVO','INACTIVO'),FIJO FROM $con->catalogo.catalogo_grupo WHERE NOMBRE like '%".$_POST["busqueda"]."%'";

	$rspaginador=$con->query("select if(DATO is null or DATO='',DATODEFAULT,DATO) as numerador from $con->catalogo.catalogo_parametro WHERE IDPARAMETRO='PAG_CATALOGOS'");
	
	$cantidadregistro=$rspaginador->fetch_object();
	$regmostrar=$cantidadregistro->numerador;
	
	$objcat = new Catalogos($sql,($regmostrar)*1,_('GRUPO'));
 
?>
<html>
	<head><title><?=_("American Assist") ;?></title>
		<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
		<script type="text/javascript" src="../../../../estilos/functionjs/ajax_catalogo.js"></script>
		<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
</head>
<body onload="document.getElementById('busqueda').focus();">
	<div id="resultado">	
	<h2><font size='3px'><?=_("CATALOGO DE GRUPOS") ;?></font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size='2px' color="#FF6666"><input type="radio" name="radio" id="radio" onClick="reDirigir('../usuarios/general.php')" value="0" title="<?=_("CATALOGO DE USUARIOS") ;?>">Usuarios&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="radio" id="radio" value="1" title="<?=_("CATALOGO DE PLANILLAS");?>" onClick="reDirigir('../perfilusuario/general.php')">Plantillas</font></font>&nbsp;&nbsp;<input type="radio" name="radio" id="radio" value="2" onClick="reDirigir('../grupos/general.php')" checked title="<?=_("CATALOGO DE GRUPOS") ;?>"><font size='2px' color="#FF6666">Configurar Grupos</font></h2>
		<?
 			//$objcat->MostrarTitulo(_('CATALOGO DE USUARIOS'));
			//$objcat->MostrarBusqueda();
			$objcat->CrearTablaCatalogo($campos,$cabecera,true,$disabled);
		 
		?>
		</div>		
	</body>
</html>