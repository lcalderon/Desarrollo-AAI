<?php
	session_start();  
	
	include_once("../../modelo/clase_lang.inc.php");
	include_once("../../modelo/clase_mysqli.inc.php");
	include_once("../../vista/login/Auth.class.php");

	$con = new DB_mysqli();	
	Auth::required();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>Informacion SOAANG</title>
	<meta name="description" content="FancyForm is a powerful checkbox replacement script used to provide the ultimate flexibility in changing the appearance and function of HTML form elements.">
	<link rel="shortcut icon" type="image/x-icon" href="../../../../imagenes/iconos/soaa.ico">	
	<link rel="stylesheet" href="../../../librerias/fancy-form/readme_files/screensmall.css" type="text/css" media="screen">
	<style type="text/css">
		<!--
		#apDiv1 {
			position:absolute;
			left:22px;
			top:34px;
			width:102px;
			height:57px;
			z-index:1;
		}
		-->
	</style> 
</head>
<body>

	<div id="apDiv1"><img src="../../../imagenes/logos/AA.jpg" border="2" /></div>  
	<hr id="topborder">
	<div id="content">
		<hr id="overview">
		<div class='section'>
			<h2>ULTIMOS CAMBIOS SOAANG</h2>
			<p class="lead">Informacion de los ultimos mejoras del <em>SOAANG</em>, nuevos aplicativos,etc.</p>
			<h3>RESUMEN</h3>
			<ul>
				<?
					$rsmensaje=$con->query("SELECT CATEGORIA,CONTENIDO FROM $con->catalogo.catalogo_mensajeinicio WHERE ACTIVO=1 ORDER BY CATEGORIA");
					while($row =$rsmensaje->fetch_object()){				
				?>
				<li><p style="text-align:justify"><strong><?=$row->CATEGORIA ?>: </strong><?=$row->CONTENIDO ?></p></li>
				<? } ?>
			</ul>
		</div>
		<hr>
		<div class='section demo'>
			<form name="myForm" action="gMensajeInicial.php" method="post">			 
				<label><input type="checkbox" name="marcaOk">No mostrar este formulario Nuevamente.</label> 						
				<input type="submit" value="Continuar..." title="Ir al SOAANG">				 
			</form>		 
		</div>
	</div> 
<script type="text/javascript" src="../../../../librerias/fancy-form/mootools.js"></script>
<script type="text/javascript" src="../../../../librerias/fancy-form/moocheck.js"></script>
</body>
</html>