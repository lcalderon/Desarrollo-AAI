<?php

	include_once('../../../modelo/clase_lang.inc.php');
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
  	//Auth::required();

	$_GET["campo"] = (isset($_GET[campo]))?$_GET[campo]:"IDPLANTILLAPERFIL";
	$_GET["orden"] = (isset($_GET[orden]))?$_GET[orden]:"ASC";
	$_GET["pag"]= (isset($_GET[pag]))?$_GET[pag]:"1";
 
	$campos="IDPLANTILLAPERFIL,NOMBRE,ACTIVO";
	$cabecera=_('ID').","._('NOMBRE').","._('STATUS');

	$IS_ADM=$con->consultation("SELECT COUNT(*) FROM $con->temporal.grupo_usuario where IDUSUARIO='".$_GET["codigo"]."' AND IDGRUPO='ADMI'");
	if($IS_ADM[0][0] ==0 and $_SESSION["user"]!="ADMINISTRADOR")	$subquery="AND IDPLANTILLAPERFIL !=1";
		
	$sql="SELECT IDPLANTILLAPERFIL,NOMBRE,if(ACTIVO=1,'ACTIVO','INACTIVO') FROM catalogo_plantillaperfil where NOMBRE like '%".$_POST["busqueda"]."%' $subquery";

	$rspaginador=$con->query("select if(DATO is null or DATO='',DATODEFAULT,DATO) as numerador from catalogo_parametro where IDPARAMETRO='PAG_CATALOGOS' ");
	
	$cantidadregistro=$rspaginador->fetch_object();
	$regmostrar=$cantidadregistro->numerador;
	
	$objcat = new Catalogos($sql,($regmostrar)*1,_('PLANTILLA'));
 
?>
<html>
<head><title>American Assist</title>
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<script type="text/javascript" src="../../../../estilos/functionjs/ajax_catalogo.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />	
</head>
<body onload="document.getElementById('busqueda').focus();">
	<div id="resultado">	
		<h2><font size='3px'><?=_("CATALOGO DE PLANTILLAS") ;?></font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size='2px' color="#FF6666"><input type="radio" name="radio" id="radio" onClick="reDirigir('../usuarios/general.php')" value="0" title="<?=_("CATALOGO DE USUARIOS") ;?>">Usuarios&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="radio" id="radio" value="1" checked title="<?=_("CATALOGO DE PLANILLAS") ;?>">Plantillas</font></font>&nbsp;&nbsp;<input type="radio" name="radio" id="radio" value="2" onClick="reDirigir('../grupos/general.php')"><font size='2px' color="#FF6666">Configurar Grupos</font></h2>

		<?
 			//$objcat->MostrarTitulo(_('CATALOGO DE USUARIOS'));
			//$objcat->MostrarBusqueda();
			$objcat->CrearTablaCatalogo($campos,$cabecera,true);
		 
		?>
	</div>		
</body>
</html>